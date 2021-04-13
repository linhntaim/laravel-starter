<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Schedules\Base;

use App\Utils\ClientSettings\Traits\AdminConsoleClientTrait;

abstract class AdminSchedule extends Schedule
{
    use AdminConsoleClientTrait;
}
