<?php

namespace App\Models;

use App\Models\Base\Model;
use App\ModelTraits\ArrayValuedAttributesTrait;
use App\Utils\ConfigHelper;
use App\Utils\HandledFiles\Storage\CloudStorage;
use App\Utils\HandledFiles\Storage\ExternalStorage;
use App\Utils\HandledFiles\Storage\HandledStorage;
use App\Utils\HandledFiles\Storage\InlineStorage;
use App\Utils\HandledFiles\Storage\IResponseStorage;
use App\Utils\HandledFiles\Storage\IUrlStorage;
use App\Utils\HandledFiles\Storage\PrivateStorage;
use App\Utils\HandledFiles\Storage\PublicStorage;
use App\Utils\HandledFiles\Storage\Storage;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class HandledFile
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $mime
 * @property bool $ready
 * @property array $options_array_value
 * @property Collection $handledFileStores
 * @property Storage $originStorage
 */
class HandledFile extends Model
{
    use ArrayValuedAttributesTrait;

    const HANDLING_YES = 1;
    const HANDLING_NO = 2;

    protected $table = 'handled_files';

    protected $fillable = [
        'name',
        'mime',
        'size',
        'options',
        'options_array_value',
        'handling',
    ];

    protected $visible = [
        'id',
        'name',
        'url',
    ];

    protected $appends = [
        'url',
    ];

    public function getReadyAttribute()
    {
        return $this->attributes['handling'] = static::HANDLING_YES;
    }

    public function getOriginStorageAttribute()
    {
        $handledFileStore = $this->handledFileStores()->where('origin', HandledFileStore::ORIGIN_YES)->first();
        if ($handledFileStore->store === PublicStorage::NAME) {
            $originStorage = (new PublicStorage())->setData($handledFileStore->data);
        } elseif ($handledFileStore->store === PrivateStorage::NAME) {
            $originStorage = (new PrivateStorage())->setData($handledFileStore->data);
        } elseif ($handledFileStore->store === InlineStorage::NAME) {
            $originStorage = (new InlineStorage())->setData($handledFileStore->data);
        } elseif ($handledFileStore->store === ExternalStorage::NAME) {
            $originStorage = (new ExternalStorage())->setData($handledFileStore->data);
        } else {
            $originStorage = ConfigHelper::get('managed_file.cloud_enabled') ? new CloudStorage() : null;
            if ($originStorage && $handledFileStore->store === $originStorage->getName()) {
                $originStorage = $originStorage->setData($handledFileStore->data);
            }
        }
        return $originStorage;
    }

    public function getUrlAttribute()
    {
        if (!$this->getReadyAttribute()) {
            return null;
        }
        return $this->tryStorage(
            function (Storage $storage, HandledFileStore $store) {
                if ($storage instanceof InlineStorage) {
                    return route('handled_file.show', ['id' => $this->id]) . '?_inline=1';
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
        parent::delete();
        $this->tryStorage(
            function (Storage $storage, HandledFileStore $store) {
                return $storage->setData($store->data)->delete();
            },
            function (Storage $storage) {
                return $storage instanceof HandledStorage;
            }
        );
        return true;
    }

    public function responseDownload($name = null, $headers = [])
    {
        if (empty($name)) $name = $this->name;

        return $this->tryStorage(
            function (Storage $storage, HandledFileStore $store) use ($name, $headers) {
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
            new ExternalStorage(),
            new InlineStorage(),
            ConfigHelper::get('managed_file.cloud_enabled') ?
                new CloudStorage() : null,
            new PublicStorage(),
            new PrivateStorage(),
        ];
        foreach ($storagePriorities as $storage) {
            if (!$storage || ($filterCallback && !$filterCallback($storage))) continue;
            if (($result = $try($storage)) !== false) {
                return $result;
            }
        }
        return null;
    }
}
