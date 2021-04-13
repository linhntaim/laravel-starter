<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

use App\Utils\ClientSettings\Traits\AdminIndependentClientTrait;

abstract class AdminNowNotification extends NowNotification
{
    use AdminIndependentClientTrait;
}