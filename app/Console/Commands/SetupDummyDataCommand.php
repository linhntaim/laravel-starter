<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;

class SetupDummyDataCommand extends Command
{
    protected $signature = 'setup:dummy-data {--u}';

    protected function go()
    {
        // TODO: Truncate data
        if (!$this->option('u')) {
            // TODO: Seed data
        }
    }
}
