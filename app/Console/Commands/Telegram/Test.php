<?php

namespace App\Console\Commands\Telegram;

use Illuminate\Console\Command;

class Test extends Command
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
        $commands = $this->telegram->getCommands();

        $text = '';
        foreach ($commands as $name => $handler) {
            $text .= sprintf('/%s - %s'.PHP_EOL, $name, $handler->getDescription());
        }

        $this->replyWithMessage(compact('text'));
    }
}
