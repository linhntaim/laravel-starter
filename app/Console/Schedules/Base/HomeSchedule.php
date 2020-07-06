<?php

namespace App\Console\Schedules;

use App\Utils\ClientSettings\HomeSettingsHandleTrait;

abstract class HomeSchedule extends Schedule
{
    use HomeSettingsHandleTrait;
}
