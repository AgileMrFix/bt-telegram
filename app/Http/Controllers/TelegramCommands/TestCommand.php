<?php

namespace App\Http\Controllers\TelegramCommands;

use Telegram\Bot\Commands\Command;

class TestCommand extends Command
{
    protected $name = 'test';

    /**
     * @var string Command Description
     */
    protected $description = 'test';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $this->replyWithMessage(['text'=>'hyjnja']);

        $text = '';


        $this->replyWithMessage(compact('text'));
    }
}
