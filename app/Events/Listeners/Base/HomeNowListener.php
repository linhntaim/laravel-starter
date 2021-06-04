<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners\Base;

use App\Utils\ClientSettings\Traits\HomeIndependentClientTrait;

abstract class HomeNowListener extends NowListener
{
    use HomeIndependentClientTrait;
}