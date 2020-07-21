<?php

namespace App\Console\Schedules\Base;

use App\Utils\ClientSettings\AdminSettingsHandleTrait;

abstract class AdminSchedule extends Schedule
{
    use AdminSettingsHandleTrait;
}
