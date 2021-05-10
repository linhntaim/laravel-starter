<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Models\Base\Model;
use App\Utils\ConfigHelper;
use App\Utils\HandledFiles\Filer\Filer;
use App\Utils\HandledFiles\Storage\CloudStorage;
use App\Utils\HandledFiles\Storage\ExternalStorage;
use App\Utils\HandledFiles\Storage\IEncryptionStorage;
use App\Utils\HandledFiles\Storage\InlineStorage;
use App\Utils\HandledFiles\Storage\IResponseStorage;
use App\Utils\HandledFiles\Storage\IUrlStorage;
use App\Utils\HandledFiles\Storage\PrivateStorage;
use App\Utils\HandledFiles\Storage\PublicStorage;
use App\Utils\HandledFiles\Storage\ScanStorage;
use App\Utils\HandledFiles\Storage\Storage;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class HandledFile
 * @package App\Models
 * @property int $id
 * @property int $handling
 * @property string $title
 * @property string $name
 * @property string $mime
 * @property string $url
 * @property bool $ready
 * @property bool $encrypted
 * @property bool $scanned
 * @property bool $scanning
 * @property bool $public
 * @property bool $inline
 * @property array $options
 * @property HandledFileStore[]|Collection $handledFileStores
 * @property Filer $filer
 * @property Storage $originStorage
 */
class HandledFile extends Model
{
    public const HANDLING_YES = 1;
    public const HANDLING_NO = 2;
    public const HANDLING_SCAN = 3;

    protected $table = 'handled_files';

    protected $fillable = [
        'created_by',
        'title',
        'name',
        'mime',
        'size',
        'options',
        'handling',
    ];

    protected $visible = [
        'id',
        'title',
        'name',
        'url',
        'ready',
    ];

    protected $appends = [
        'url',
        'ready',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function getReadyAttribute()
    {
        return $this->attributes['handling'] == static::HANDLING_NO;
    }

    public function getFilerAttribute()
    {
        return $this->remind('filer', function () {
            $filer = new Filer();
            $filer->setEncryped($this->encrypted);
            $this->handledFileStores->each(function (HandledFileStore $store) use ($filer) {
                $filer->fromStorageData($store->store, $store->data, $store->isOrigin, $this->encrypted);
            });
            return $filer;
        });
    }

    public function getOriginStorageAttribute()
    {
        return $this->filer->getOriginStorage();
    }

    public function getEncryptedAttribute()
    {
        return isset($this->options['encrypt']) && $this->options['encrypt'];
    }

    public function getScannedAttribute()
    {
        return (!isset($this->options['scan']) || $this->options['scan'] == false)
            && (!isset($this->options['scanned']) || $this->options['scanned']);
    }

    public function getScanningAttribute()
    {
        return isset($this->options['scan']) && $this->options['scan'];
    }

    public function getPublicAttribute()
    {
        return isset($this->options['public']) && $this->options['public'];
    }

    public function getInlineAttribute()
    {
        return isset($this->options['inline']) && $this->options['inline'];
    }

    public function getUrlAttribute()
    {
        if (!$this->ready || !$this->scanned) {
            return null;
        }
        if (!$this->public || $this->encrypted) {
            return route('api.account.handled_file.show', ['id' => $this->id]) . '?_inline=1';
        }
        if ($this->inline) {
            return route('api.handled_file.show', ['id' => $this->id]) . '?_inline=1';
        }
        return $this->tryStorage(
            function (Storage $storage, HandledFileStore $store) {
                if ($storage instanceof InlineStorage) {
                    return route('api.handled_file.show', ['id' => $this->id]) . '?_inline=1';
                }
                return $storage->setData($store->data)->getUrl();
            },
            function (Storage $storage) {
                return $storage instanceof IUrlStorage;
            }
        );
    }

    public function handledFileStores()
    {
        return $this->hasMany(HandledFileStore::class, 'handled_file_id', 'id');
    }

    public function delete()
    {
        $this->filer->delete();
        return parent::delete();
    }

    public function responseDownload($name = null, $headers = [])
    {
        if (empty($name)) {
            $name = $this->name;
        }

        return $this->tryStorage(
            function (Storage $storage, HandledFileStore $store) use ($name, $headers) {
                if (ConfigHelper::get('handled_file.encryption.enabled')) {
                    if ($storage instanceof IEncryptionStorage) {
                        $storage->setEncrypted($this->encrypted);
                    }
                }
                return $storage->setData($store->data)->responseDownload($name, $this->mime, $headers);
            },
            function (Storage $storage) {
                return $storage instanceof IResponseStorage;
            }
        );
    }

    public function responseFile($headers = [])
    {
        return $this->tryStorage(
            function (Storage $storage, HandledFileStore $store) use ($headers) {
                if (ConfigHelper::get('handled_file.encryption.enabled')) {
                    if ($storage instanceof IEncryptionStorage) {
                        $storage->setEncrypted($this->encrypted);
                    }
                }
                return $storage->setData($store->data)->responseFile($this->mime, $headers);
            },
            function (Storage $storage) {
                return $storage instanceof IResponseStorage;
            }
        );
    }

    public function tryStorage(callable $tryCallback, callable $filterCallback = null)
    {
        $handledFileStores = $this->handledFileStores;
        $try = function (Storage $storage) use ($handledFileStores, $tryCallback) {
            if ($store = $handledFileStores->firstWhere('store', $storage->getName())) {
                return $tryCallback($storage, $store);
            }
            return false;
        };
        $storagePriorities = [
            ConfigHelper::get('handled_file.scan.enabled') ?
                new ScanStorage() : null,
            new ExternalStorage(),
            new InlineStorage(),
            ConfigHelper::get('handled_file.cloud.enabled') ?
                new CloudStorage() : null,
            new PublicStorage(),
            new PrivateStorage(),
        ];
        foreach ($storagePriorities as $storage) {
            if (!$storage || ($filterCallback && !$filterCallback($storage))) {
                continue;
            }
            if (($result = $try($storage)) !== false) {
                return $result;
            }
        }
        return null;
    }
}
