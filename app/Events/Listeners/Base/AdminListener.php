<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners\Base;

use App\Utils\ClientSettings\Traits\AdminIndependentClientTrait;

abstract class AdminListener extends Listener
{
    use AdminIndependentClientTrait;
}