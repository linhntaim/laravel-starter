<?php

namespace App\Console\Schedules;

use App\Console\Schedules\Base\CommandSchedule;

class TestCommandSchedule extends CommandSchedule
{
    protected function getCommand()
    {
        return 'test:schedule';
    }
}