<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications;

use App\Models\Base\IUser;
use App\Notifications\Base\DatabaseNotificationTrait;
use App\Notifications\Base\IDatabaseNotification;
use App\Notifications\Base\NowNotification;
use Illuminate\Notifications\DatabaseNotification;

class TestNotification extends NowNotification implements IDatabaseNotification
{
    use DatabaseNotificationTrait;

    public static function makeFromModel(DatabaseNotification $notification)
    {
        return new static();
    }

    public function getTitle(IUser $notifiable)
    {
        return static::__transWithCurrentModule('title');
    }

    public function getContent(IUser $notifiable, $html = true)
    {
        return static::__transWithCurrentModule('content');
    }
}