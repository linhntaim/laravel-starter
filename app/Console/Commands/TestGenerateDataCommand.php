<?php

namespace App\Console\Commands;

class TestGenerateDataCommand extends Command
{
    protected $signature = 'test:generate-data {--max=10}';

    protected $max;

    protected function go()
    {
    }
}
