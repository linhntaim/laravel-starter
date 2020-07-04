<?php

namespace App\Models;

use App\Models\Base\Model;
use App\Utils\ClientSettings\Facade;

/**
 * Class DataExport
 * @package App\Models
 * @property int $id
 * @property int $state
 * @property string sdStCreatedAt
 * @property ManagedFile $managedFile
 */
class DataExport extends Model
{
    const STATE_EXPORTING = 1;
    const STATE_EXPORTED = 2;
    const STATE_FAILED = 3;

    protected $table = 'data_exports';

    protected $fillable = [
        'created_by',
        'state',
        'name',
        'managed_file_id',
        'payload',
    ];

    protected $visible = [
        'id',
        'url',
    ];

    protected $appends = [
        'url',
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

    public function managedFile()
    {
        return $this->hasOne(ManagedFile::class, 'id', 'managed_file_id');
    }
}
