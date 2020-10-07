<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\ModelResources\ActivityLogResource;
use App\Models\Base\Model;
use App\ModelTraits\ArrayValuedAttributesTrait;
use App\Utils\ClientSettings\Facade;

/**
 * Class ActivityLog
 * @package App\Models
 * @property Device $device
 * @property string $sdStCreatedAt
 * @property array $screens_array_value
 * @property array $payload_array_value
 */
class ActivityLog extends Model
{
    use ArrayValuedAttributesTrait;

    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_LIST = 'list';
    const ACTION_SEARCH = 'search';
    const ACTION_CREATE = 'create';
    const ACTION_EDIT = 'edit';
    const ACTION_DELETE = 'delete';
    const ACTION_EXPORT = 'export';

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'device_id',
        'client',
        'screen',
        'action',
        'screens',
        'screens_array_value',
        'screens_overridden_array_value',
        'payload',
        'payload_array_value',
        'payload_overridden_array_value',
    ];

    protected $visible = [
        'id',
        'client',
        'screen',
        'action',
        'sd_st_created_at',
    ];

    protected $appends = [
        'sd_st_created_at',
    ];

    protected $resourceClass = ActivityLogResource::class;

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'user_id');
    }

    public function getSdStCreatedAtAttribute()
    {
        $dateTimer = Facade::dateTimer();
        return $dateTimer->compound('shortDate', ' ', 'shortTime', $this->attributes['created_at']);
    }
}
