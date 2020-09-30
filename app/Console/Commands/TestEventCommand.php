<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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
