<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;

class TestGenerateDataCommand extends Command
{
    protected $signature = 'test:generate-data {--max=10}';

    protected $max;

    protected function go()
    {
    }
}
