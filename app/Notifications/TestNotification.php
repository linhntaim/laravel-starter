<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications;

use App\Notifications\Base\Notification;

class TestNotification extends Notification
{
    public function shouldBroadcast()
    {
        return true;
    }
}
