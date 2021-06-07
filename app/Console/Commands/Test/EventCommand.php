<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Test;

use App\Console\Commands\Base\Command;
use App\Events\TestEvent;

class EventCommand extends Command
{
    protected $signature = 'test:event';

    protected function go()
    {
        event(new TestEvent());
    }
}
