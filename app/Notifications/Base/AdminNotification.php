<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

use App\Utils\ClientSettings\Traits\AdminIndependentClientTrait;

abstract class AdminNotification extends Notification
{
    use AdminIndependentClientTrait;
}