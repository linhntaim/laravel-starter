<?php

namespace App\Console\Schedules\Base;

use App\Utils\ClientSettings\AdminSettingsHandleTrait;

abstract class AdminCommandSchedule extends CommandSchedule
{
    use AdminSettingsHandleTrait;
}
