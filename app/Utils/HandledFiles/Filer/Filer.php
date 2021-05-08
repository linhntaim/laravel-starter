<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Filer;

use App\Exceptions\AppException;
use App\Utils\ClassTrait;
use App\Utils\ConfigHelper;
use App\Utils\HandledFiles\File;
use App\Utils\HandledFiles\Helper;
use App\Utils\HandledFiles\Storage\CloudStorage;
use App\Utils\HandledFiles\Storage\ExternalStorage;
use App\Utils\HandledFiles\Storage\HandledStorage;
use App\Utils\HandledFiles\Storage\IEncryptionStorage;
use App\Utils\HandledFiles\Storage\InlineStorage;
use App\Utils\HandledFiles\Storage\LocalStorage;
use App\Utils\HandledFiles\Storage\PrivateStorage;
use App\Utils\HandledFiles\Storage\PublicStorage;
use App\Utils\HandledFiles\Storage\ScanStorage;
use App\Utils\HandledFiles\Storage\Storage;
use App\Utils\HandledFiles\StorageManager\StorageManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Filer
{
    use ClassTrait, ResourceFilerTrait, WriteFilerTrait, ReadFilerTrait;

    public const MODE_READ = 'r';
    public const MODE_WRITE = 'w';
    public const MODE_WRITE_APPEND = 'a';

    /**
     * @var StorageManager
     */
    protected $storageManager;

    protected $name;

    protected $mime;

    protected $size;

    protected $encryped = false;

    public function __construct()
    {
        $this->storageManager = new StorageManager();
    }

    public function setName($name)
    {
        if ($name) {
            $this->name = $name;
        }
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMime()
    {
        if (is_null($this->mime)) {
            $this->mime = $this->storageManager->originMime();
        }
        return $this->mime;
    }

    public function getSize()
    {
        if (is_null($this->size)) {
            $this->size = $this->storageManager->originSize();
        }
        return $this->size;
    }

    public function setEncryped($encryped = true)
    {
        $this->encryped = $encryped;
        return $this;
    }

    public function encrypted()
    {
        return $this->encryped;
    }

    public function eachStorage(callable $callback)
    {
        $this->storageManager->each($callback);
        return $this;
    }

    public function getOriginStorage()
    {
        return $this->storageManager->origin();
    }

    public function handled()
    {
        return ($originStorage = $this->getOriginStorage()) && $originStorage instanceof HandledStorage ? $originStorage : null;
    }

    /**
     * @throws AppException
     */
    protected function checkIfCanInitializeFromAnySource()
    {
        if ($this->storageManager->stored()) {
            throw new AppException('Cannot initialize from new source');
        }
    }

    public function getDefaultToDirectory()
    {
        return Helper::concatPath(date('Y'), date('m'), date('d'), date('H'));
    }

    protected function parseToDirectory($toDirectory, $default = null)
    {
        return is_string($toDirectory) ? $toDirectory :
            ($toDirectory === false ? $this->getDefaultToDirectory() : $default);
    }

    /**
     * @param Storage $storage
     * @param bool $markOriginal
     * @return Filer
     * @throws AppException
     */
    public function fromStorage(Storage $storage, $markOriginal = true)
    {
        $this->checkIfCanInitializeFromAnySource();

        $this->storageManager->add($storage, $markOriginal);
        return $this;
    }

    /**
     * @param string $storageName
     * @param string $data
     * @param bool $markOriginal
     * @param bool $encrypted
     * @return Filer
     * @throws AppException
     */
    public function fromStorageData($storageName, $data, $markOriginal = true, $encrypted = false)
    {
        if ($markOriginal) {
            $this->checkIfCanInitializeFromAnySource();
        }

        $availableStorages = [
            ($storage = new ExternalStorage())->getName() => $storage,
            ($storage = new InlineStorage())->getName() => $storage,
            ($storage = new PublicStorage())->getName() => $storage,
            ($storage = new PrivateStorage())->getName() => $storage,
        ];
        if (ConfigHelper::get('handled_file.scan.enabled')) {
            $availableStorages[($storage = new ScanStorage())->getName()] = $storage;
        }
        if (ConfigHelper::get('handled_file.cloud.enabled')) {
            $availableStorages[($storage = new CloudStorage())->getName()] = $storage;
        }

        if (isset($availableStorages[$storageName]) && !is_null($storage = $availableStorages[$storageName])) {
            if ($storage instanceof IEncryptionStorage) {
                $storage->setEncrypted($encrypted);
            }
            $this->storageManager->add($storage->setData($data), $markOriginal);
        }
        return $this;
    }

    /**
     * @param string $url
     * @return Filer
     * @throws AppException
     */
    public function fromExternal($url)
    {
        $this->name = basename($url);
        return $this->fromStorage((new ExternalStorage())->fromUrl($url));;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param bool|string|null $toDirectory
     * @return Filer
     * @throws AppException
     */
    public function fromExistedBlob(UploadedFile $uploadedFile, $toDirectory = null)
    {
        return $this->fromExisted($uploadedFile, $toDirectory, false);
    }

    /**
     * @param UploadedFile|File|string $file
     * @param bool|string|null $toDirectory
     * @param bool|string|array $keepOriginalName
     * @return Filer
     * @throws AppException
     */
    public function fromExisted($file, $toDirectory = null, $keepOriginalName = true)
    {
        if ($file instanceof UploadedFile) {
            $originalName = $file->getClientOriginalName();
        } elseif ($file instanceof File) {
            $originalName = $file->getBasename();
        } else {
            $originalName = basename($file);
        }
        $this->name = $originalName;

        if (is_string($file)) {
            if (!is_file($file)) {
                throw new AppException('File was not found');
            }
            $filePath = $file;
        } elseif ($file instanceof File) {
            $filePath = $file->getRealPath();
        }
        if (isset($filePath)) {
            $try = function (LocalStorage $storage) use ($filePath, $toDirectory) {
                $rootPath = $storage->getRootPath();
                if (($relativePath = Helper::noWrappedSlashes(Str::after($filePath, $rootPath))) != Helper::noWrappedSlashes($filePath)) {
                    $this->fromStorage($storage->setRelativePath($relativePath)->move($toDirectory));
                    return true;
                }
                return false;
            };
            if ($try(new PublicStorage())) {
                return $this;
            }
            if ($try(new PrivateStorage())) {
                return $this;
            }
        }

        return $this->fromStorage((new PrivateStorage())->from($file, $this->parseToDirectory($toDirectory, ''), $keepOriginalName));
    }

    /**
     * @param string $name
     * @param string $extension
     * @param bool|string|null $toDirectory
     * @return Filer
     * @throws AppException
     */
    public function fromCreating($name = null, $extension = null, $toDirectory = false)
    {
        $this->name = Helper::nameWithExtension($name, $extension);
        return $this->fromStorage((new PrivateStorage())->create(
            $extension,
            $this->parseToDirectory($toDirectory, '')
        ));
    }

    public function makeOriginalStorage(Storage $storage = null)
    {
        return $this->fromStorage($storage ?: new PrivateStorage());
    }

    public function moveTo($toDirectory = null, $keepOriginalName = true, $override = true, callable $overrideCallback = null)
    {
        if (($originStorage = $this->handled()) && $originStorage instanceof HandledStorage) {
            $originStorage->move($this->parseToDirectory($toDirectory, ''), $keepOriginalName, $override, $overrideCallback);
        }
        return $this;
    }

    public function copyTo($toDirectory = null, $keepOriginalName = true, $override = true, callable $overrideCallback = null)
    {
        if (($originStorage = $this->handled()) && $originStorage instanceof HandledStorage) {
            $originStorage->copy($this->parseToDirectory($toDirectory, ''), $keepOriginalName, $override, $overrideCallback);
        }
        return $this;
    }

    public function encrypt($encrypted = true)
    {
        if ($encrypted && ConfigHelper::get('handled_file.encryption.enabled') && !$this->encryped) {
            // cache before encrypting
            $this->getSize(); // cache before encrypting
            $this->getMime();
            // encrypting
            $this->eachStorage(function ($name, Storage $storage) {
                if ($storage instanceof IEncryptionStorage) {
                    $storage->encrypt();
                }
            });

            $this->encryped = true;
        }
        return $this;
    }

    public function decrypt($decrypted = true)
    {
        if ($decrypted && ConfigHelper::get('handled_file.encryption.enabled') && $this->encryped) {
            $this->eachStorage(function ($name, Storage $storage) {
                if ($storage instanceof IEncryptionStorage) {
                    $storage->decrypt();
                }
            });
            $this->encryped = true;
        }
        return $this;
    }

    public function moveToHandledStorage(HandledStorage $toStorage,
                                         $toDirectory = 'asda',
                                         $keepOriginalName = true,
                                         $markOriginal = true,
                                         $cloneOriginal = false,
                                         $visibility = 'public')
    {
        if (($originStorage = $this->handled())) {
            if ($originStorage instanceof HandledStorage) {
                $toCloudFromPrivate = $toStorage instanceof CloudStorage && $originStorage instanceof PrivateStorage;
                $toDirectory = $this->parseToDirectory($toDirectory, $originStorage->getRelativeDirectory());
                if ($toCloudFromPrivate) {
                    $visibility = 'private';
                    $toDirectory = 'private' . ($toDirectory ? DIRECTORY_SEPARATOR . $toDirectory : '');
                }
                $this->changeOriginalStorage(
                    $originStorage,
                    $toStorage->from(
                        $originStorage,
                        $toDirectory,
                        $keepOriginalName,
                        $visibility
                    ),
                    $markOriginal,
                    $cloneOriginal
                );
            }
        }
        return $this;
    }

    public function moveToScan($toDirectory = null, $keepOriginalName = true)
    {
        if (ConfigHelper::get('handled_file.scan.enabled')) {
            return $this->moveToHandledStorage(
                new ScanStorage(),
                $toDirectory,
                $keepOriginalName
            );
        }
        return $this;
    }

    public function moveToPublic($toDirectory = null, $keepOriginalName = true, $markOriginal = true, $cloneOriginal = false)
    {
        return $this->moveToHandledStorage(
            new PublicStorage(),
            $toDirectory,
            $keepOriginalName,
            $markOriginal,
            $cloneOriginal
        );
    }

    public function copyToPublic($toDirectory = null, $keepOriginalName = true)
    {
        return $this->moveToPublic($toDirectory, $keepOriginalName, false);
    }

    public function cloneToPublic($toDirectory = null, $keepOriginalName = true)
    {
        return $this->moveToPublic($toDirectory, $keepOriginalName, true, true);
    }

    public function moveToPrivate($toDirectory = null, $keepOriginalName = true, $markOriginal = true, $cloneOriginal = false)
    {
        return $this->moveToHandledStorage(
            new PrivateStorage(),
            $toDirectory,
            $keepOriginalName,
            $markOriginal,
            $cloneOriginal
        );
    }

    public function copyToPrivate($toDirectory = null, $keepOriginalName = true)
    {
        return $this->moveToPrivate($toDirectory, $keepOriginalName, false);
    }

    public function cloneToPrivate($toDirectory = null, $keepOriginalName = true)
    {
        return $this->moveToPrivate($toDirectory, $keepOriginalName, true, true);
    }

    public function moveToCloud($toDirectory = null, $keepOriginalName = true, $markOriginal = true, $cloneOriginal = false)
    {
        if (ConfigHelper::get('handled_file.cloud.enabled')) {
            return $this->moveToHandledStorage(
                new CloudStorage(),
                $toDirectory,
                $keepOriginalName,
                $markOriginal,
                $cloneOriginal
            );
        }
        return $this;
    }

    public function copyToCloud($toDirectory = null, $keepOriginalName = true)
    {
        return $this->moveToCloud($toDirectory, $keepOriginalName, false);
    }

    public function cloneToCloud($toDirectory = null, $keepOriginalName = true)
    {
        return $this->moveToCloud($toDirectory, $keepOriginalName, true, true);
    }

    public function moveToInline($markOriginal = true, $cloneOriginal = false)
    {
        if (($originStorage = $this->handled())) {
            $toStorage = new InlineStorage();
            $this->changeOriginalStorage(
                $originStorage,
                $toStorage->from($originStorage),
                $markOriginal,
                $cloneOriginal
            );
        }
        return $this;
    }

    public function copyToInline()
    {
        return $this->moveToInline(false);
    }

    public function cloneToInline()
    {
        return $this->moveToInline(true, true);
    }

    protected function changeOriginalStorage($fromStorage, $toStorage, $markOriginal = true, $cloneOriginal = false)
    {
        if ($markOriginal) {
            if (!$cloneOriginal) {
                $fromStorage->delete();
            }
            $this->storageManager->removeOrigin();
        }
        $this->storageManager->add($toStorage, $markOriginal);
        return $this;
    }

    public function delete()
    {
        if (($originStorage = $this->handled())) {
            $originStorage->delete();
            $this->storageManager->clear();
            $this->name = null;
        }
        return $this;
    }

    public function __destruct()
    {
        $this->fClose();
    }
}
