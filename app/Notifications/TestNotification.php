<?php

namespace App\Notifications;

use App\Notifications\Base\Notification;

class TestNotification extends Notification
{
    public function shouldBroadcast()
    {
        return true;
    }
}
