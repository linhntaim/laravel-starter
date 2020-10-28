<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Test;

use App\Console\Commands\Base\Command;

class GenerateDataCommand extends Command
{
    protected $signature = 'test:generate-data {--max=10}';

    protected $max;

    protected function go()
    {
    }
}
