<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

class AdminNotification extends Notification
{
    use AdminNotificationTrait;
}
