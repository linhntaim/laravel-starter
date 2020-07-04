<?php

namespace App\Models;

use App\Models\Base\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class DataExport
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property bool $inLocal
 * @property bool $inSelfHandledLocal
 * @property bool $inFreeLocal
 * @property bool $inCloud
 * @property bool $inCloudOnly
 * @property string $url
 * @property string $rawUrl
 */
class ManagedFile extends Model
{
    protected $table = 'managed_files';

    protected $fillable = [
        'name',
        'size',
        'type',
        'local_disk',
        'local_url',
        'local_path',
        'cloud_disk',
        'cloud_url',
        'cloud_path',
        'inline',
    ];

    protected $visible = [
        'id',
        'name',
        'url',
    ];

    protected $appends = [
        'url',
    ];

    public function getInLocalAttribute()
    {
        return !empty($this->attributes['local_disk']);
    }

    public function getInSelfHandledLocalAttribute()
    {
        return empty($this->attributes['local_disk'])
            && !empty($this->attributes['local_path'])
            && !empty($this->attributes['local_url']);
    }

    public function getInFreeLocalAttribute()
    {
        return empty($this->attributes['local_disk'])
            && empty($this->attributes['local_path'])
            && !empty($this->attributes['local_url']);
    }

    public function getInCloudAttribute()
    {
        return !empty($this->attributes['cloud_disk']);
    }

    public function getInCloudOnlyAttribute()
    {
        return empty($this->attributes['local_disk'])
            && empty($this->attributes['local_path'])
            && !empty($this->attributes['cloud_disk']);
    }

    public function getUrlAttribute()
    {
        return empty($this->attributes['inline']) ?
            (!empty($this->attributes['cloud_url']) ?
                $this->attributes['cloud_url'] : $this->attributes['local_url'])
            : route('managed_file.show', ['id' => $this->id]) . '?_image=1';
    }

    public function getRawUrlAttribute()
    {
        return empty($this->attributes['inline']) ?
            (!empty($this->attributes['cloud_url']) ?
                $this->attributes['cloud_url'] : $this->attributes['local_url'])
            : sprintf('data:%s;base64,%s', $this->attributes['type'], $this->attributes['inline']);
    }

    public function setInlineAttribute($value)
    {
        $this->attributes['inline'] = base64_encode($value);
    }

    public function responseDownload($name = null, $headers = [])
    {
        if (empty($name)) $name = $this->name;
        if (!empty($this->attributes['inline'])) {
            return response(base64_decode($this->attributes['inline']), 200, [
                'Content-Type' => $this->type,
                'Content-Disposition' => 'attachment',
            ]);
        }
        if ($this->inCloudOnly) {
            return Storage::disk($this->attributes['cloud_disk'])
                ->download($this->attributes['cloud_path'], $name, $headers);
        }
        if ($this->inLocal) {
            return Storage::disk($this->attributes['local_disk'])
                ->download($this->attributes['local_path'], $name, $headers);
        }
        if ($this->inFreeLocal) {
            return response()->streamDownload(function () {
                echo $this->attributes['local_url'];
            }, $name, [
                'Content-Type' => $this->type,
            ]);
        }
        return response()->download($this->attributes['local_path'], $name, $headers);
    }

    public function responseFile($headers = [])
    {
        if (!empty($this->attributes['inline'])) {
            return response(base64_decode($this->attributes['inline']), 200, [
                'Content-Type' => $this->type,
                'Content-Disposition' => 'inline',
            ]);
        }
        if ($this->inCloudOnly) {
            return Storage::disk($this->attributes['cloud_disk'])
                ->response($this->attributes['cloud_path'], $this->name, $headers);
        }
        if ($this->inLocal) {
            return Storage::disk($this->attributes['local_disk'])
                ->response($this->attributes['local_path'], $this->name, $headers);
        }
        if ($this->inFreeLocal) {
            return response()->streamDownload(function () {
                echo file_get_contents($this->attributes['local_url']);
            }, null, [
                'Content-Type' => $this->type,
            ], 'inline');
        }
        return response()->download($this->attributes['local_path'], $this->name, $headers, 'inline');
    }

    public function delete()
    {
        parent::delete();
        if ($this->inCloud) {
            Storage::disk($this->attributes['cloud_disk'])
                ->delete($this->attributes['cloud_path']);
        }
        if ($this->inLocal) {
            Storage::disk($this->attributes['local_disk'])
                ->delete($this->attributes['local_path']);
        } elseif ($this->inSelfHandledLocal) {
            if (file_exists($this->attributes['local_path'])) {
                unlink($this->attributes['local_path']);
            }
        }
        return true;
    }
}
