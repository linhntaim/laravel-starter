<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Models\Base\Model;

/**
 * Class HandledFile
 * @package App\Models
 * @property int $id
 * @property string $store
 * @property string $data
 * @property bool $isOrigin
 */
class HandledFileStore extends Model
{
    public const ORIGIN_YES = 1;
    public const ORIGIN_NO = 2;

    protected $table = 'handled_file_stores';

    protected $fillable = [
        'handled_file_id',
        'origin',
        'store',
        'data',
    ];

    protected $visible = [
        'handled_file_id',
    ];

    public $timestamps = false;

    public function getIsOriginAttribute()
    {
        return $this->attributes['origin'] == static::ORIGIN_YES;
    }

    public function handledFile()
    {
        return $this->belongsTo(HandledFile::class, 'handled_file_id', 'id');
    }
}
