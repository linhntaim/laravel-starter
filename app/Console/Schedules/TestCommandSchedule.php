<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Schedules;

use App\Console\Schedules\Base\CommandSchedule;

class TestCommandSchedule extends CommandSchedule
{
    protected function getCommand()
    {
        return 'test:schedule';
    }
}