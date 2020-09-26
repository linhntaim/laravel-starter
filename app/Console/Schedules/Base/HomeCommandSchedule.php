<?php

namespace App\Console\Schedules\Base;

use App\Utils\ClientSettings\HomeSettingsHandleTrait;

abstract class HomeCommandSchedule extends CommandSchedule
{
    use HomeSettingsHandleTrait;
}
