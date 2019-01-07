<?php

namespace App\Http\Controllers;

use App\Models\Telegram\TelegramUser;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Methods\Update;
use Telegram\Bot\Objects\Message;

class WebhookController extends Controller
{

    protected $update;
    /**
     * @var $telegramUser TelegramUser
     */
    protected $telegramUser;
    protected $message;

    /**
     * @var $textProcessing TextProcessing
     */
    protected $textProcessing;

    public function processWebhook()
    {

        $this->update = Telegram::bot()->getWebhookUpdate();
        Log::info($this->update);
        return 'ok';

        $this->telegramUser = $this->getTelegramUser();
        $this->saveMessageHistory();

        $this->textProcessing = new TextProcessing($this->update, $this->message, $this->telegramUser);

        $this->processMessage();
        Log::info('good');
        return 'ok';

    }

    public function testUpdate()
    {
        $this->update = Telegram::getUpdates()[0];
        $this->telegramUser = $this->getTelegramUser();
        $this->saveMessageHistory();
    }

    /**
     * @return TelegramUser
     */
    protected function getTelegramUser()
    {
        if ($this->update->has('message'))
            $from = $this->update['message']['from'];

        if ($this->update->has('edited_message'))
            $from = $this->update['edited_message']['from'];


        $telegramUser = TelegramUser::find($from['id']);

        if (is_null($telegramUser))
            $telegramUser = TelegramUser::create($this->obj2arr($from));


        return $telegramUser;
    }

    protected function saveMessageHistory()
    {
        $data = [];
        if ($this->update->has('edited_message')) {
            $data['edited'] = true;
            $message = $this->update['edited_message'];
        } elseif ($this->update->has('message')) {
            $data['edited'] = false;
            $message = $this->update['message'];
        } else {
            return;
        }

        $data['message_id'] = $message['message_id'];
        $data['type'] = '';
        $data['text'] = !$message->has('text') ?: $message['text'];


        $this->telegramUser->message_histories()->create($data);
    }

    protected function processMessage()
    {

        $this->message = $this->update->getMessage();
        if ($this->message === null) {
            return;
        }

        $partMessage = null;
        switch (true) {
            case $this->message->has('text') :
                if ($this->messageIsCommand()) {
                    $this->commandProcessing($this->message['text']);
                    return;
                }
                $this->textProcessing->processText($this->message['text']);
                $this->textProcessing->sendMessage('ok');
                break;
            case $this->message->has('audio'):
                $partMessage = trans('telegram.message_types_description.audio');
                break;
            case $this->message->has('document'):
                $partMessage = trans('telegram.message_types_description.document');
                break;
            case $this->message->has('photo'):
                $partMessage = trans('telegram.message_types_description.photo');
                break;
            case $this->message->has('sticker'):
                $partMessage = trans('telegram.message_types_description.sticker');
                break;
            case $this->message->has('video'):
                $partMessage = trans('telegram.message_types_description.video');
                break;
            case $this->message->has('voice'):
                $partMessage = trans('telegram.message_types_description.voice');
                break;
            case $this->message->has('contact'):
                $partMessage = trans('telegram.message_types_description.contact');
                break;
            case $this->message->has('location'):
                $partMessage = trans('telegram.message_types_description.location');
                break;
            default:
                $partMessage = trans('telegram.message_types_description.other');
        }

        $this->sendErrorMessage($partMessage);

    }

    protected function sendErrorMessage($partMessage)
    {
        if (is_null($partMessage)) {
            return;
        }
        $this->textProcessing->sendMessage(trans('telegram.errors.process', ['message_type' => $partMessage]));
    }

    /**
     * @param $update
     * @return bool
     */
    protected function messageIsCommand()
    {
        if ($this->message['text'][0] === '/') {
            return true;
        }
        return false;
    }

    protected function commandProcessing($command)
    {
        switch ($command) {
            case '/start':
                if (!$this->telegramUser->allowed)
                    $this->textProcessing->sendMessage(trans('telegram.check_security_code.need'));
                break;
        }
        return;
    }

    /**
     * @return array
     */
    protected function obj2arr($obj)
    {
        return json_decode(json_encode($obj), true);
    }


}
