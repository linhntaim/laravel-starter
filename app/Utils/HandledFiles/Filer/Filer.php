<?php

namespace App\Utils\HandledFiles\Filer;

use App\Exceptions\AppException;
use App\Utils\ClassTrait;
use App\Utils\ConfigHelper;
use App\Utils\HandledFiles\File;
use App\Utils\HandledFiles\Helper;
use App\Utils\HandledFiles\Storage\CloudStorage;
use App\Utils\HandledFiles\Storage\ExternalStorage;
use App\Utils\HandledFiles\Storage\HandledStorage;
use App\Utils\HandledFiles\Storage\InlineStorage;
use App\Utils\HandledFiles\Storage\LocalStorage;
use App\Utils\HandledFiles\Storage\PrivateStorage;
use App\Utils\HandledFiles\Storage\PublicStorage;
use App\Utils\HandledFiles\Storage\Storage;
use App\Utils\HandledFiles\StorageManager\StorageManager;
use App\Utils\HandledFiles\StorageManager\StrictStorageManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Filer
{
    use ClassTrait, ResourceFilerTrait, WriteFilerTrait, ReadFilerTrait;

    const MODE_READ = 'r';
    const MODE_WRITE = 'w';
    const MODE_WRITE_APPEND = 'a';

    /**
     * @var StorageManager
     */
    protected $storageManager;

    protected $name;

    public function __construct()
    {
        $this->storageManager = new StrictStorageManager();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSize()
    {
        return $this->storageManager->originSize();
    }

    public function getMime()
    {
        return $this->storageManager->originMime();
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

    protected function getDefaultToDirectory()
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
     * @return $this
     * @throws AppException
     */
    public function fromStorage(Storage $storage)
    {
        $this->checkIfCanInitializeFromAnySource();

        $this->storageManager->add($storage, true);
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
        return $this->fromStorage($storage ? $storage : new PrivateStorage());
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

    protected function moveToHandledStorage(HandledStorage $toStorage, callable $conditionCallback = null, $toDirectory = null, $keepOriginalName = true, $markOriginal = true)
    {
        if (($originStorage = $this->handled())) {
            if (is_null($conditionCallback) || $conditionCallback($originStorage)) {
                if (!$this->storageManager->exists($toStorage->getName())) {
                    $toStorage->from(
                        $originStorage->getRealPath(),
                        $this->parseToDirectory($toDirectory, $originStorage->getRelativeDirectory()),
                        $keepOriginalName
                    );
                    if ($markOriginal) {
                        $originStorage->delete();
                        $this->storageManager->removeOrigin();
                    }
                    $this->storageManager->add($toStorage, $markOriginal);
                }
            }
        }
        return $this;
    }

    public function moveToPublic($toDirectory = null, $keepOriginalName = true, $markOriginal = true)
    {
        return $this->moveToHandledStorage(
            new PublicStorage(),
            function ($originStorage) {
                return $originStorage instanceof PrivateStorage;
            },
            $toDirectory, $keepOriginalName, $markOriginal
        );
    }

    public function copyToPublic($toDirectory = null, $keepOriginalName = true)
    {
        return $this->moveToPublic($toDirectory, $keepOriginalName, false);
    }

    public function moveToPrivate($toDirectory = null, $keepOriginalName = true, $markOriginal = true)
    {
        return $this->moveToHandledStorage(
            new PrivateStorage(),
            function ($originStorage) {
                return $originStorage instanceof PublicStorage;
            },
            $toDirectory, $keepOriginalName, $markOriginal
        );
    }

    public function copyToPrivate($toDirectory = null, $keepOriginalName = true)
    {
        return $this->moveToPrivate($toDirectory, $keepOriginalName, false);
    }

    public function moveToCloud($toDirectory = null, $keepOriginalName = true, $markOriginal = true)
    {
        if (ConfigHelper::get('handled_file.cloud.enabled')) {
            return $this->moveToHandledStorage(
                new CloudStorage(),
                function ($originStorage) {
                    return $originStorage instanceof LocalStorage;
                },
                $toDirectory, $keepOriginalName, $markOriginal
            );
        }
        return $this;
    }

    public function copyToCloud($toDirectory = null, $keepOriginalName = true)
    {
        return $this->moveToCloud($toDirectory, $keepOriginalName, false);
    }

    public function moveToInline($markOriginal = true)
    {
        if (($originStorage = $this->handled())) {
            $toStorage = new InlineStorage();
            if (!$this->storageManager->exists($toStorage->getName())) {
                $toStorage->fromContent($originStorage->getContent());
                if ($markOriginal) {
                    $originStorage->delete();
                    $this->storageManager->removeOrigin();
                }
                $this->storageManager->add($toStorage, $markOriginal);
            }
        }
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
