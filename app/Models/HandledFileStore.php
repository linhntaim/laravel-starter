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
 */
class HandledFileStore extends Model
{
    const ORIGIN_YES = 1;
    const ORIGIN_NO = 2;

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

    public function handledFile()
    {
        return $this->belongsTo(HandledFile::class, 'handled_file_id', 'id');
    }
}
