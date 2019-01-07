<?php

namespace App\Http\Controllers\TelegramCommands;

use Telegram\Bot\Commands\Command;

/**
 * Class HelpCommand.
 */
class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'start';

    /**
     * @var string Command Description
     */
    protected $description = 'Start bot';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {

    }
}
