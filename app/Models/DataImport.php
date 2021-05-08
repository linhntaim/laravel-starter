<?php

namespace App\Models;

use App\Models\Base\Model;
use App\Utils\ClientSettings\Facade;

/**
 * Class DataImport
 * @package App\Models
 * @property int $id
 * @property int $state
 * @property string sdStCreatedAt
 * @property HandledFile $file
 */
class DataImport extends Model
{
    public const STATE_IMPORTING = 1;
    public const STATE_IMPORTED = 2;
    public const STATE_FAILED = 3;

    protected $table = 'data_imports';

    protected $fillable = [
        'created_by',
        'state',
        'name',
        'file_id',
        'exception',
    ];

    protected $visible = [
        'id',
        'state',
        'sd_st_created_at',
    ];

    protected $appends = [
        'sd_st_created_at',
    ];

    protected $casts = [
        'exception' => 'array',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function file()
    {
        return $this->hasOne(HandledFile::class, 'id', 'file_id');
    }
}
