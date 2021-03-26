<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Models\Base\Model;
use App\ModelTraits\ArrayValuedAttributesTrait;
use App\Utils\ConfigHelper;
use App\Utils\HandledFiles\Storage\CloudStorage;
use App\Utils\HandledFiles\Storage\ExternalStorage;
use App\Utils\HandledFiles\Storage\HandledStorage;
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
 * @property bool $public
 * @property bool $inline
 * @property array $options_array_value
 * @property Collection $handledFileStores
 * @property Storage $originStorage
 */
class HandledFile extends Model
{
    use ArrayValuedAttributesTrait;

    const HANDLING_YES = 1;
    const HANDLING_NO = 2;
    const HANDLING_SCAN = 3;

    protected $table = 'handled_files';

    protected $fillable = [
        'title',
        'name',
        'mime',
        'size',
        'options',
        'options_array_value',
        'options_overridden_array_value',
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

    public function getReadyAttribute()
    {
        return $this->attributes['handling'] == static::HANDLING_NO;
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
        } elseif ($handledFileStore->store === ScanStorage::NAME) {
            $originStorage = (new ScanStorage())->setData($handledFileStore->data);
        } else {
            $originStorage = ConfigHelper::get('handled_file.cloud.enabled') ? new CloudStorage() : null;
            if ($originStorage && $handledFileStore->store === $originStorage->getName()) {
                $originStorage = $originStorage->setData($handledFileStore->data);
            }
        }
        return $originStorage;
    }

    public function getEncryptedAttribute()
    {
        return isset($this->options_array_value['encrypt']) && $this->options_array_value['encrypt'];
    }

    public function getPublicAttribute()
    {
        return isset($this->options_array_value['public']) && $this->options_array_value['public'];
    }

    public function getInlineAttribute()
    {
        return isset($this->options_array_value['inline']) && $this->options_array_value['inline'];
    }

    public function getUrlAttribute()
    {
        if (!$this->getReadyAttribute()) {
            return null;
        }
        if (!$this->public) {
            return route('account.handled_file.show', ['id' => $this->id]) . '?_inline=1';
        }
        if ($this->encrypted || $this->inline) {
            return route('handled_file.show', ['id' => $this->id]) . '?_inline=1';
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
                if ($storage instanceof IEncryptionStorage) {
                    $storage->setEncrypted();
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
                if ($storage instanceof IEncryptionStorage) {
                    $storage->setEncrypted();
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
            if (!$storage || ($filterCallback && !$filterCallback($storage))) continue;
            if (($result = $try($storage)) !== false) {
                return $result;
            }
        }
        return null;
    }
}
