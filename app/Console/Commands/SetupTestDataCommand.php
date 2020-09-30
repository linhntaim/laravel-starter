<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;

class SetupTestDataCommand extends Command
{
    protected $signature = 'setup:test-data {--u}';

    protected function go()
    {
        // TODO: Truncate data

        // TODO
        if (!$this->option('u')) {
            // TODO: Seed data

            // TODO
        }
    }
}
