<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Schedules;

use App\Console\Schedules\Base\ShellSchedule;

class TestShellSchedule extends ShellSchedule
{
    protected function getShell()
    {
        return 'echo "Hello world!"';
    }
}