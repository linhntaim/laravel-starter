<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use App\Notifications\Base\Notification;
use Illuminate\Notifications\Events\NotificationSent;

class OnNotificationSent extends NowListener
{
    /**
     * @param NotificationSent $event
     */
    protected function go($event)
    {
        $this->getNotification($event)->afterNotifying($event->channel, $event->notifiable);
    }

    /**
     * @param NotificationSent $event
     * @return Notification
     */
    protected function getNotification($event)
    {
        return $event->notification;
    }
}