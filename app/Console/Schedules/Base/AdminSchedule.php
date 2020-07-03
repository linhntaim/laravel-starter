<?php

namespace App\Console\Schedules;

use App\Utils\Traits\AdminSettingsHandleTrait;

abstract class AdminSchedule extends Schedule
{
    use AdminSettingsHandleTrait;
}
