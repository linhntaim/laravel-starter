<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

use App\Exceptions\AppException;
use Illuminate\Notifications\DatabaseNotification;

class DatabaseNotificationFactory
{
    /**
     * @param DatabaseNotification $notification
     * @return NowNotification|mixed
     * @throws AppException
     */
    public static function makeFromModel(DatabaseNotification $notification)
    {
        $notificationClass = $notification->type;
        if (in_array(IDatabaseNotification::class, class_implements($notificationClass))) {
            return $notificationClass::makeFromModel($notification);
        }
        throw new AppException(sprintf('Notification class `%s` does not implement `%s`', $notificationClass, IDatabaseNotification::class));
    }
}