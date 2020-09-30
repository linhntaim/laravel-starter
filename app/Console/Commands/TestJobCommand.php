<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\Jobs\TestingJob;

class TestJobCommand extends Command
{
    protected $signature = 'test:job';

    protected function go()
    {
        TestingJob::dispatch();
    }
}
