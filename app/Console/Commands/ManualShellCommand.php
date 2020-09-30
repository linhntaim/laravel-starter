<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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
