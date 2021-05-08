<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\ModelResources\ActivityLogResource;
use App\Models\Base\IActivityLog;
use App\Models\Base\Model;
use App\Utils\ClientSettings\Facade;
use Illuminate\Support\Str;

/**
 * Class ActivityLog
 * @package App\Models
 * @property Device $device
 * @property User $user
 * @property Admin $admin
 * @property string $sdStCreatedAt
 * @property array $screens
 * @property array $payload
 */
class ActivityLog extends Model
{
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_MODEL_LIST = 'model_list';
    public const ACTION_MODEL_EXPORT = 'model_export';
    public const ACTION_MODEL_IMPORT = 'model_import';
    public const ACTION_MODEL_CREATE = 'model_create';
    public const ACTION_MODEL_EDIT = 'model_edit';
    public const ACTION_MODEL_DELETE = 'model_delete';
    // TODO: Add actions

    // TODO

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'device_id',
        'client',
        'screen',
        'action',
        'screens',
        'payload',
    ];

    protected $visible = [
        'id',
        'client',
        'screen',
        'action',
        'log',
        'sd_st_created_at',
    ];

    protected $appends = [
        'log',
        'sd_st_created_at',
    ];

    protected $casts = [
        'screens' => 'array',
        'payload' => 'array',
    ];

    protected $resourceClass = ActivityLogResource::class;

    public function getSdStCreatedAtAttribute()
    {
        $dateTimer = Facade::dateTimer();
        return $dateTimer->compound('shortDate', ' ', 'shortTime', $this->attributes['created_at']);
    }

    public function getLogAttribute()
    {
        $method = sprintf('get%sLogAttribute', Str::studly($this->attributes['action']));
        return method_exists($this, $method) ? $this->{$method}() : $this->attributes['payload'];
    }

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

    /**
     * @param IActivityLog|Model|\Illuminate\Database\Eloquent\Model $model
     * @param string $separator
     * @param string $prefix
     * @return string
     */
    protected function listedWithModel($model, $separator = PHP_EOL, $prefix = '')
    {
        return $this->listed($model->toActivityLogArray(), $separator, $prefix, function ($key) {
            return sprintf('model.%s', $key);
        });
    }

    /**
     * @param array $array
     * @param string $separator
     * @param string $prefix
     * @param callable|null $keyCallback
     * @return string
     */
    protected function listed(array $array, $separator = PHP_EOL, $prefix = '', callable $keyCallback = null)
    {
        $listed = [];
        foreach ($array as $key => $value) {
            if (!is_null($value) && !Str::startsWith($key, '_')) {
                $listed[] = sprintf(
                    '%s%s: %s',
                    $prefix,
                    trans($keyCallback ? $keyCallback($key) : sprintf('label.%s', $key)),
                    is_array($value) ? implode(', ', $value) : $value
                );
            }
        }
        return $separator . implode($separator, $listed);
    }

    public function getLoginLogAttribute()
    {
        return trans('activity_log.login', [
            'log' => $this->listedWithModel($this->user, '<br>', '- '),
        ]);
    }

    public function getLogoutLogAttribute()
    {
        return trans('activity_log.logout', [
            'log' => $this->listedWithModel($this->user, '<br>', '- '),
        ]);
    }

    public function getModelListLogAttribute()
    {
        $modelClass = $this->payload['model'];
        return trans('activity_log.model_list.' . $modelClass, [
            'log' => $this->listed($this->payload['params'], '<br>', '- ', function ($key) use ($modelClass) {
                $modelKey = sprintf('model.%s.%s', $modelClass, $key);
                $labelKey = sprintf('label.%s', $key);
                return trans()->has($modelKey) ? $modelKey
                    : (trans()->has($labelKey) ? $labelKey : $key);
            }),
        ]);
    }

    public function getModelExportLogAttribute()
    {
        $modelClass = $this->payload['model'];
        return trans('activity_log.model_export.' . $modelClass, [
            'log' => $this->listed($this->payload['params'], '<br>', '- ', function ($key) use ($modelClass) {
                $modelKey = sprintf('model.%s.%s', $modelClass, $key);
                $labelKey = sprintf('label.%s', $key);
                return trans()->has($modelKey) ? $modelKey
                    : (trans()->has($labelKey) ? $labelKey : $key);
            }),
        ]);
    }

    public function getModelCreateLogAttribute()
    {
        $modelClass = $this->payload['model'];
        return trans('activity_log.model_create.' . $modelClass, [
            'log' => $this->listed($this->payload['created'], '<br>', '- ', function ($key) use ($modelClass) {
                return sprintf('model.%s', $key);
            }),
        ]);
    }

    public function getModelEditLogAttribute()
    {
        $modelClass = $this->payload['model'];
        return trans('activity_log.model_edit.' . $modelClass, [
            'log' => '<br>' . trans('activity_log.model_edit.old')
                . $this->listed($this->payload['old'], '<br>', '- ', function ($key) use ($modelClass) {
                    return sprintf('model.%s', $key);
                })
                . '<br>' . trans('activity_log.model_edit.edited')
                . $this->listed($this->payload['edited'], '<br>', '- ', function ($key) use ($modelClass) {
                    return sprintf('model.%s', $key);
                }),
        ]);
    }

    public function getModelDeleteLogAttribute()
    {
        $modelClass = $this->payload['model'];
        return trans('activity_log.model_delete.' . $modelClass, [
            'log' => (function ($deleted) use ($modelClass) {
                $log = '';
                $count = count($deleted);
                foreach ($deleted as $index => $d) {
                    $log .= ($count > 1 ? '<br>[' . ($index + 1) . ']' : '')
                        . $this->listed($d, '<br>', '- ', function ($key) use ($modelClass) {
                            return sprintf('model.%s', $key);
                        });
                }
                return $log;
            })($this->payload['deleted']),
        ]);
    }

    // TODO: Add log message methods

    // TODO
}
