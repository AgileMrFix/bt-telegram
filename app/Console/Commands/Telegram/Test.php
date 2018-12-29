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
        $this->replyWithMessage(['text'=>'hyjnja']);

        $text = '';


        $this->replyWithMessage(compact('text'));
    }
}
