<?php

namespace App\Console\Schedules;

use App\Console\Schedules\Base\CommandSchedule;

class ScanHandleFilesSchedule extends CommandSchedule
{
    protected function getCommand()
    {
        return 'scan:handled_files';
    }
}