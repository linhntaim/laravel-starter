<?php

namespace App\Models;

use App\Utils\ClientSettings\DateTimer;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DataExport
 * @package App\Models
 * @property string sdStCreatedAt
 * @property int $state
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

    public function getSdStCreatedAtAttribute()
    {
        return DateTimer::getInstance()
            ->compound(
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

    public function managedFile()
    {
        return $this->hasOne(ManagedFile::class, 'id', 'managed_file_id');
    }
}
