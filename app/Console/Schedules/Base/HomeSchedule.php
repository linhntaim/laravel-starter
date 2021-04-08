<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Schedules\Base;

use App\Utils\ClientSettings\HomeClientTrait;

abstract class HomeSchedule extends Schedule
{
    use HomeClientTrait;
}
