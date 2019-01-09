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
            case Step::TYPE_WAIT:
                $this->checkWaitData();
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

                $nextActionData = Step::getDataForEmployee($action + 1);
                $reply_markup = $this->getKeyboard($nextActionData['keyboard']);
                $this->sendMessage($nextActionData['message'], $reply_markup);
                $this->setStep($this->step->type, $data, $action + 1);

                break;
            case 1:
                $actionData = Step::getDataForEmployee($action);
                $data = $this->unitStepData([$actionData['name'] => $this->text]);

                $nextActionData = Step::getDataForEmployee($action + 1);
                $reply_markup = $this->getKeyboard($nextActionData['keyboard']);
                $this->sendMessage($nextActionData['message'], $reply_markup);
                $this->setStep($this->step->type, $data, $action + 1);
                break;
            case 2:
                $actionData = Step::getDataForEmployee($action);

                if (($department_id = $this->validateDepartment()) === false) {
                    $this->sendMessage(trans('telegram.after_registration.error'));
                    return;
                }
                $data = $this->unitStepData([$actionData['name'] => $department_id]);
                $employee = $this->telegramUser->employee()->create(json_decode($data, true));

                $reply_markup = $this->getMainKeyboard();
                $this->sendMessage(
                    trans(
                        'telegram.after_registration.success',
                        [
                            'Fist_name' => ucfirst(strtolower($employee->first_name)),
                            'Last_name' => ucfirst(strtolower($employee->last_name)),
                        ]
                    ),
                    $reply_markup
                );

                $this->setStep(Step::TYPE_WAIT);
                break;
        }
    }

    protected function checkWaitData()
    {
        switch ($this->text) {
            case 'Розповісти анекдот':
                $text = file_get_contents('http://rzhunemogu.ru/RandJSON.aspx?CType=1');
                $text = iconv('CP1251', 'UTF-8', $text);
                $text = preg_replace('/\s\s+/', ' ', $text);
                $text = json_decode($text, true);
                $message = array_key_exists('content', $text) ? $text['content'] : null;
                $reply_markup = $this->getMainKeyboard();
                $this->sendMessage($message, $reply_markup);
                break;
            case 'Запропонувати функцію':
                $this->setStep(Step::TYPE_SUGGESTION);
                $message = ["Уважно слухаю!", "Які будуть побажання?"];
                $reply_markup = $this->getKeyboard();
                break;
            default:
                $message = 'Тобі краще обрати команду із запропонованих';
                $reply_markup = $this->getMainKeyboard();
        }
        $this->sendMessage($message, $reply_markup);
    }

    protected function checkSuggestionData()
    {
        $this->setStep(Step::TYPE_WAIT);
        $message = 'Дякую, прийму до уваги.';
        $this->sendMessage($message, $this->getMainKeyboard());
    }


    /**
     * @param $newPart array
     * @return string
     */
    protected function unitStepData($newPart)
    {
        $data = json_decode($this->step->data, true);
        $data = array_merge($data, $newPart);
        return json_encode($data);
    }

    protected function validateDepartment()
    {
        $rules = Department::all()->pluck('name', 'id')->toArray();
        $search = array_search($this->text, $rules);

        if (is_null($search))
            return false;

        return $search;
    }

    public function sendMessage($texts, $reply_markup = null, $chat_id = null)
    {
        $reply_markup = $reply_markup ?? $this->getKeyboard();
        $chat_id = is_null($chat_id) ? $this->telegramUser->id : $chat_id;
        if (gettype($texts) == gettype([])) {
            foreach ($texts as $text) {
                Telegram::sendMessage(compact('chat_id', 'text', 'reply_markup'));
            }

        } else {
            $text = $texts;
            Telegram::sendMessage(compact('chat_id', 'text', 'reply_markup'));
        }
        return;
    }

    protected function getKeyboard($keyboard = null)
    {
        if (is_null($keyboard))
            return Telegram::replyKeyboardMarkup(['remove_keyboard' => true]);

        $reply_markup = [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ];
        return Telegram::replyKeyboardMarkup($reply_markup);
    }

    protected function getMainKeyboard()
    {
        $keyboard = [
            ['Розповісти анекдот'],
            ['Запропонувати функцію']
        ];
        return $this->getKeyboard($keyboard);
    }


}
