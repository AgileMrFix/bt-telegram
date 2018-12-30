<?php

namespace App\Http\Controllers;

use Telegram\Bot\Laravel\Facades\Telegram;

class WebhookController extends Controller
{
    public function processWebhook()
    {
        $update = Telegram::commandsHandler(true);

        if ($this->messageIsCommand($update)) {
            return 'ok';
        }

        $id = $update['message']['from']['id'];
        $this->sendMessage($id);

        return 'ok';

    }

    /**
     * @param $update
     * @return bool
     */
    protected function messageIsCommand($update)
    {
        $message = $update->getMessage();

        if ($message !== null && $message->has('text') && $message[0] === '/') {
            return true;

        }

        return false;
    }

    protected function saveMessageHistory($update)
    {

    }

    protected function sendMessage($id)
    {
        return Telegram::sendMessage(['text' => 'Не шали!', 'chat_id' => $id]);
    }
}
