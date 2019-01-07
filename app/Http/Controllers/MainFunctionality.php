<?php
/**
 * Created by PhpStorm.
 * User: fixpe
 * Date: 07.01.2019
 * Time: 18:50
 */

namespace App\Http\Controllers;


use App\Models\Telegram\SecurityCode;
use App\Models\Telegram\TelegramUser;
use Telegram\Bot\Laravel\Facades\Telegram;

class MainFunctionality
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
        $this->checkSecurityCode();


    }

    protected function checkSecurityCode()
    {
        if ($this->telegramUser->allowed)
            return;

        if (SecurityCode::query()->where('code', $this->text)->count()) {
            $this->sendMessage(trans('telegram.check_security_code.success'));
            $this->telegramUser->allowed = true;
            $this->telegramUser->save();
            return;
        }

        $this->sendMessage(trans('telegram.check_security_code.error'));
        return;
    }

    public function sendMessage($text, $chat_id = null)
    {
        $chat_id = is_null($chat_id) ? $this->telegramUser->id : $chat_id;
        return Telegram::sendMessage(compact('chat_id', 'text'));
    }


}
