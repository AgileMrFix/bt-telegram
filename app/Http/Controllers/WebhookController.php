<?php

namespace App\Http\Controllers;

use App\Models\Telegram\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebhookController extends Controller
{

    protected $update;
    /**
     * @var $telegramUser TelegramUser
     */
    protected $telegramUser;

    public function processWebhook()
    {
        $this->update = Telegram::commandsHandler(true);

        $this->telegramUser = $this->getTelegramUser();
        $this->saveMessageHistory();
//        $this->processMessage();

        return 'ok';

    }

    public function testUpdate(){

        $json = file_get_contents('https://api.telegram.org/bot712285308:AAET4okFG1d1CTu6vENUZJ7ybNUMqECep8M/getUpdates');

        $this->update = json_decode($json)->result[0];
        $this->telegramUser = $this->getTelegramUser();


    }

    /**
     * @return TelegramUser
     */
    protected function getTelegramUser()
    {

        $from = $this->update->message->from;
        $telegramUser = TelegramUser::find($from->id);
        if (is_null($telegramUser)) {
            $telegramUser = TelegramUser::create((array) $from);
        }

        return $telegramUser;
    }

    protected function saveMessageHistory()
    {
        $data = [];

        if ($this->update->has('edited_message')) {
            $data['edited'] = true;
            $message = $this->update->edited_message;
        } elseif ($this->update->has('message')) {
            $data['edited'] = false;
            $message = $this->update->message;
        } else {
            return;
        }


        $data['message_id'] = $message->message_id;
        $data['type'] = $this->update->isMessageType();
        $data['text'] = !$message->has('text') ?: $message->text;

        $this->telegramUser->message_histories()->create($data);
    }

    protected function processMessage()
    {
        $message = $this->update->getMessage();
        if ($message === null) {
            return;
        }

        switch (true) {
            case $message->has('text') :
                if ($this->messageIsCommand($message)) {
                    return;
                }
                $this->sendMessage('ok');
                break;
            case $message->has('audio'):
                $partMessage = trans('telegram.message_types_description.audio');
                break;
            case $message->has('document'):
                $partMessage = trans('telegram.message_types_description.document');
                break;
            case $message->has('photo'):
                $partMessage = trans('telegram.message_types_description.photo');
                break;
            case $message->has('sticker'):
                $partMessage = trans('telegram.message_types_description.sticker');
                break;
            case $message->has('video'):
                $partMessage = trans('telegram.message_types_description.video');
                break;
            case $message->has('voice'):
                $partMessage = trans('telegram.message_types_description.voice');
                break;
            case $message->has('contact'):
                $partMessage = trans('telegram.message_types_description.contact');
                break;
            case $message->has('location'):
                $partMessage = trans('telegram.message_types_description.location');
                break;
            default:
                $partMessage = trans('telegram.message_types_description.other');
        }

        $this->sendErrorMessage($partMessage);


    }

    protected function sendErrorMessage($partMessage = null)
    {
        if (is_null($partMessage)) {
            return;
        }

        $this->sendMessage(trans('telegram.errors.process', ['message_type' => $partMessage]));
    }

    /**
     * @param $update
     * @return bool
     */
    protected function messageIsCommand($message)
    {
        if ($message['text'][0] === '/') {
            return true;
        }
        return false;
    }

    protected function sendMessage($text, $chat_id = null)
    {
        $chat_id = is_null($chat_id) ? $this->telegramUser->id : $chat_id;
        return Telegram::sendMessage(compact('chat_id', 'text'));
    }
}
