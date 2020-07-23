<?php

namespace App\Models;

use App\Models\Base\Model;

/**
 * Class HandledFile
 * @package App\Models
 * @property int $id
 * @property string $name
 */
class HandledFile extends Model
{
    protected $table = 'handled_files';

    protected $fillable = [
        'name',
        'mime',
        'size',
    ];

    protected $visible = [
        'id',
        'name',
        'url',
    ];

    protected $appends = [
        'url',
    ];

    public function handledFileStores()
    {
        return $this->hasMany(HandledFileStore::class, 'handled_file_id', 'id');
    }
}
