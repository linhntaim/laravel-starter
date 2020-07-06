<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;

class SetupTestDataCommand extends Command
{
    protected $signature = 'setup:test-data {--u}';

    protected function go()
    {
        // TODO: Truncate data
        if (!$this->option('u')) {
            // TODO: Seed data
        }
    }
}
