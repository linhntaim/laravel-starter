<?php

namespace App\Console\Schedules;

use App\Utils\Traits\HomeSettingsHandleTrait;

abstract class HomeSchedule extends Schedule
{
    use HomeSettingsHandleTrait;
}
