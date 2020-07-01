<?php

namespace App\Console\Commands;

use App\Utils\Files\FileHelper;
use App\Utils\StringHelper;

class TestGenerateDataCommand extends Command
{
    protected $signature = 'test:generate-data {--max=10}';

    protected $max;

    protected function go()
    {
    }
}
