<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\Events\TestingEvent;

class TestEventCommand extends Command
{
    protected $signature = 'test:event';

    protected function go()
    {
        event(new TestingEvent());
    }
}
