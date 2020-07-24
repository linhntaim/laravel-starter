<?php

namespace App\Models;

use App\Models\Base\Model;

/**
 * Class HandledFile
 * @package App\Models
 * @property int $id
 * @property string $name
 */
class HandledFileStore extends Model
{
    protected $table = 'handled_file_stores';

    protected $fillable = [
        'handled_file_id',
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
