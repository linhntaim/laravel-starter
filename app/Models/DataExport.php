<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Models\Base\Model;
use App\Utils\ClientSettings\Facade;

/**
 * Class DataExport
 * @package App\Models
 * @property int $id
 * @property int $state
 * @property string sdStCreatedAt
 * @property HandledFile $file
 */
class DataExport extends Model
{
    public const STATE_EXPORTING = 1;
    public const STATE_EXPORTED = 2;
    public const STATE_FAILED = 3;

    protected $table = 'data_exports';

    protected $fillable = [
        'created_by',
        'state',
        'name',
        'file_id',
        'payload',
    ];

    protected $visible = [
        'id',
        'url',
        'state',
        'sd_st_created_at',
    ];

    protected $appends = [
        'url',
        'sd_st_created_at',
    ];

    public function getSdStCreatedAtAttribute()
    {
        return Facade::dateTimer()->compound(
            'shortDate',
            ' ',
            'shortTime',
            $this->attributes['created_at']
        );
    }

    public function getUrlAttribute()
    {
        return url('api/admin/data-export', [$this->id]) . '?_download=1';
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function file()
    {
        return $this->hasOne(HandledFile::class, 'id', 'file_id');
    }
}
