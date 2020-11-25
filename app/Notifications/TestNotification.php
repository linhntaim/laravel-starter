<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications;

use App\Models\Base\IUser;
use App\Models\DatabaseNotification;
use App\Notifications\Base\DatabaseNotificationTrait;
use App\Notifications\Base\IDatabaseNotification;
use App\Notifications\Base\NotificationActions;
use App\Notifications\Base\NowNotification;

class TestNotification extends NowNotification implements IDatabaseNotification
{
    use DatabaseNotificationTrait;

    protected $test;

    public static function makeFromModel(DatabaseNotification $notification)
    {
        return new static($notification->getDataByKey('test', 'test'));
    }

    public function __construct($test = 'test', IUser $notifier = null)
    {
        parent::__construct($notifier);

        $this->test = $test;
    }

    protected function dataDatabase(IUser $notifiable)
    {
        return array_merge(parent::dataDatabase($notifiable), [
            'test' => $this->test,
        ]);
    }

    public function getContent(IUser $notifiable, $html = true)
    {
        return static::__transWithCurrentModule('content', [
            'test' => $this->test,
        ]);
    }

    public function getAction(IUser $notifiable)
    {
        return NotificationActions::actionGo(
            'test',
            [
                'test' => $this->test,
            ],
            [
                'test' => $this->test,
            ],
            'test'
        );
    }
}
