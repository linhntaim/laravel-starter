<?php

namespace App\Console\Schedules;

use App\Console\Schedules\Base\ShellSchedule;

class TestShellSchedule extends ShellSchedule
{
    protected function getShell()
    {
        return 'echo "Hello world!"';
    }
}