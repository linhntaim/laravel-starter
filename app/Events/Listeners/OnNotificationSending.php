<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use App\Notifications\Base\Notification;
use Illuminate\Notifications\Events\NotificationSending;

class OnNotificationSending extends NowListener
{
    /**
     * @param NotificationSending $event
     */
    protected function go($event)
    {
        $this->getNotification($event)->beforeNotifying($event->channel, $event->notifiable);
    }

    /**
     * @param NotificationSending $event
     * @return Notification
     */
    protected function getNotification($event)
    {
        return $event->notification;
    }
}