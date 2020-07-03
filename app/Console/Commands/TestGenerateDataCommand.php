<?php

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
