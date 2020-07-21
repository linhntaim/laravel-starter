<?php

namespace App\Console\Schedules\Base;

use App\Utils\ClientSettings\HomeSettingsHandleTrait;

abstract class HomeSchedule extends Schedule
{
    use HomeSettingsHandleTrait;
}
