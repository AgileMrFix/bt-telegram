<?php
/**
 * Created by PhpStorm.
 * User: fixpe
 * Date: 07.01.2019
 * Time: 18:50
 */

namespace App\Http\Controllers;


use App\Models\Telegram\Department;
use App\Models\Telegram\SecurityCode;
use App\Models\Telegram\TelegramUser;
use App\Step;
use Telegram\Bot\Laravel\Facades\Telegram;

class TextProcessing
{

    protected $update;
    protected $message;
    /**
     * @var $telegramUser TelegramUser
     */
    protected $telegramUser;

    /**@var $text string */
    protected $text;
    protected $step;

    /**
     * MainFunctionality constructor.
     * @param $update
     * @param $message
     * @param $telegramUser TelegramUser
     */
    public function __construct($update, $message, $telegramUser)
    {
        $this->update = $update;
        $this->message = $message;
        $this->telegramUser = $telegramUser;

        $stepRelation = $telegramUser->step();
        $this->step = $stepRelation->count() ? $stepRelation->first() : $stepRelation->create();

    }

    public function processText($text)
    {
        $this->text = $text;

        if (!$this->telegramUser->allowed) {
            $this->checkSecurityCode();
            return;
        }

        switch ($this->step->type) {
            case Step::TYPE_EMPLOYEE:
                $this->checkEmployeeData();
                break;
            case Step::TYPE_DEPARTMENT:
                break;
        }

    }

    protected function checkSecurityCode()
    {

        if (SecurityCode::query()->where('code', $this->text)->count()) {
            $this->sendMessage(trans('telegram.check_security_code.success'));
            $this->telegramUser->allowed = true;
            $this->telegramUser->save();

            $this->setStep(Step::TYPE_EMPLOYEE);
            $this->sendMessage(Step::getDataForEmployee($this->step->action)['message']);
            return;
        }


        $this->sendMessage(trans('telegram.check_security_code.error'));
        return;
    }

    public function sendMessage($text, $reply_markup = null, $chat_id = null)
    {
        $chat_id = is_null($chat_id) ? $this->telegramUser->id : $chat_id;
        return Telegram::sendMessage(compact('chat_id', 'text', 'reply_markup'));
    }

    protected function setStep($type, $data = "[]", $action = 0)
    {
        $this->step->update(compact('type', 'data', 'action'));
    }

    protected function checkEmployeeData()
    {
        switch ($action = $this->step->action) {
            case 0:
                $actionData = Step::getDataForEmployee($action);
                $data = $this->unitStepData([$actionData['name'] => $this->text]);
                $this->setStep($this->step->type, $data, $action++);

                $nextActionData = Step::getDataForEmployee($action++);
                $reply_markup = $this->getMainKeyboard($nextActionData['keyboard']);
                $this->sendMessage($nextActionData['message'], $reply_markup);

                break;
            case 1:
                $actionData = Step::getDataForEmployee($action);
                $data = $this->unitStepData([$actionData['name'] => $this->text]);
                $this->setStep($this->step->type, $data, $action++);

                $nextActionData = Step::getDataForEmployee($action++);
                $reply_markup = $this->getKeyboard($nextActionData['keyboard']);
                $this->sendMessage($nextActionData['message'], $reply_markup);
                break;
            case 2:
                $actionData = Step::getDataForEmployee($action);
                $data = $this->unitStepData([$actionData['name'] => $this->text]);

                if ($this->validateText($this->text, Department::all()->pluck('name'))) {
                    $this->setStep(Step::TYPE_WAIT);
                    $employee = $this->telegramUser->employee()->create($data);

                    $reply_markup = $this->getMainKeyboard();
                    $this->sendMessage(
                        trans(
                            'telegram.after_registration.success',
                            [
                                'Fist_name' => $employee->first_name,
                                'Last_name' => $employee->last_name
                            ]
                        ),
                        $reply_markup
                    );

                }

                break;
        }
    }


    /**
     * @param $newPart array
     * @return string
     */
    protected function unitStepData($newPart)
    {
        $data = json_decode($this->step->data, true);
        $date = array_merge($data, $newPart);
        return json_encode($data);
    }

    protected function validateText($text, $rules)
    {
        $search = array_search($text, $rules);

        if (is_null($search))
            return false;

        return true;
    }

    protected function getKeyboard($data=null)
    {
        if (is_null($data))
            return json_encode(['remove_keyboard'=>true]);

        return Telegram::replyKeyboardMarkup($data);
    }

    protected function getMainKeyboard()
    {
        $data = null;
        return $this->getKeyboard($data);
    }


}
