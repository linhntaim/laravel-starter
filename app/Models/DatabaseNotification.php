<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Models\Base\IUser;
use App\ModelTraits\MemorizeTrait;
use App\Notifications\Base\DatabaseNotificationFactory;
use App\Notifications\Base\NowNotification;
use App\Utils\ClientSettings\Facade;
use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;

/**
 * Class DatabaseNotification
 * @package App\Models
 * @property string $type
 * @property string $name
 * @property string $image
 * @property string $title
 * @property string $content
 * @property string $htmlContent
 * @property string $action
 * @property string $sdStCreatedAt
 * @property string $sdStReadAt
 * @property array|null $data
 * @property IUser $notifiable
 * @property NowNotification|mixed $notification
 */
class DatabaseNotification extends BaseDatabaseNotification
{
    use MemorizeTrait;

    protected $visible = [
        'id',
        'name',
        'image',
        'title',
        'content',
        'html_content',
        'action',
        'sd_st_created_at',
        'sd_st_read_at',
    ];

    protected $appends = [
        'name',
        'image',
        'title',
        'content',
        'html_content',
        'action',
        'sd_st_created_at',
        'sd_st_read_at',
    ];

    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }

    public function getNotifiableAttribute()
    {
        return $this->remind('notifiable', function () {
            return $this->notifiable()->first();
        });
    }

    /**
     * @return NowNotification|mixed
     * @throws
     */
    public function getNotificationAttribute()
    {
        return $this->remind('notification', function () {
            return DatabaseNotificationFactory::makeFromModel($this);
        });
    }

    public function getNameAttribute()
    {
        return $this->remind('name', function () {
            return $this->notification->getName();
        });
    }

    public function getImageAttribute()
    {
        return $this->remind('image', function () {
            return $this->notification->getImage($this->notifiable);
        });
    }

    public function getTitleAttribute()
    {
        return $this->remind('title', function () {
            return $this->notification->getTitle($this->notifiable);
        });
    }

    public function getContentAttribute()
    {
        return $this->remind('content', function () {
            return $this->notification->getContent($this->notifiable, false);
        });
    }

    public function getHtmlContentAttribute()
    {
        return $this->remind('html_content', function () {
            return $this->notification->getContent($this->notifiable);
        });
    }

    public function getActionAttribute()
    {
        return $this->remind('action', function () {
            return $this->notification->getAction($this->notifiable);
        });
    }

    public function getSdStCreatedAtAttribute()
    {
        return Facade::dateTimer()->compound(
            'shortDate', ' ', 'shortTime', $this->attributes['created_at']
        );
    }

    public function getSdStReadAtAttribute()
    {
        return $this->read() ? Facade::dateTimer()->compound(
            'shortDate', ' ', 'shortTime', $this->attributes['read_at']
        ) : null;
    }

    public function getDataByKey($key, $default = null)
    {
        $data = $this->data;
        return isset($data[$key]) ? $data[$key] : $default;
    }
}
