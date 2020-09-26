<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\ShellCommand;

class ManualShellCommand extends ShellCommand
{
    protected $signature = 'shell:manual {shell}';

    protected function getShell()
    {
        return $this->argument('shell');
    }
}
