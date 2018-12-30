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
        $text = 'test message '.trans('telegram.commands.test.test');

        $this->replyWithMessage(compact('text'));
    }
}