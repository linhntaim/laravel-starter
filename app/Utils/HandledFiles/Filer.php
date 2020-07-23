<?php

namespace App\Utils\HandledFiles;

use App\Exceptions\AppException;
use App\Utils\HandledFiles\Storage\CloudStorage;
use App\Utils\HandledFiles\Storage\ExternalStorage;
use App\Utils\HandledFiles\Storage\HandledStorage;
use App\Utils\HandledFiles\Storage\InlineStorage;
use App\Utils\HandledFiles\Storage\LocalStorage;
use App\Utils\HandledFiles\Storage\PrivateStorage;
use App\Utils\HandledFiles\Storage\PublicStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Filer
{
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

    public function getOriginStorage()
    {
        return $this->storageManager->origin();
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

    /**
     * @param string $url
     * @return Filer
     * @throws AppException
     */
    public function fromExternal($url)
    {
        $this->checkIfCanInitializeFromAnySource();

        $this->name = basename($url);
        $this->storageManager->add(new ExternalStorage($url), true);

        return $this;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $toDirectory
     * @param bool $keepOriginalName
     * @return Filer
     * @throws AppException
     */
    public function fromUploaded(UploadedFile $uploadedFile, $toDirectory = 'upload', $keepOriginalName = true)
    {
        return $this->fromExisted($uploadedFile, $toDirectory, $keepOriginalName);
    }

    /**
     * @param $file
     * @param string $toDirectory
     * @param bool $keepOriginalName
     * @return Filer
     * @throws AppException
     */
    public function fromExisted($file, $toDirectory = null, $keepOriginalName = true)
    {
        $this->checkIfCanInitializeFromAnySource();

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
                if (($relativePath = Helper::noWrappedSlashes(Str::after($filePath, $rootPath))) != $filePath) {
                    $this->storageManager->add($storage->setRelativePath($relativePath)->move($toDirectory), true);
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

        $this->storageManager->add((new PrivateStorage())->from($file, is_null($toDirectory) ? '' : $toDirectory, $keepOriginalName), true);

        return $this;
    }

    /**
     * @param string $name
     * @param string $extension
     * @param string $toDirectory
     * @return Filer
     * @throws AppException
     */
    public function fromCreating($name, $extension, $toDirectory = '')
    {
        $this->checkIfCanInitializeFromAnySource();

        $this->name = Helper::nameWithExtension($name, $extension);
        $this->storageManager->add((new PrivateStorage())->create($extension, $toDirectory), true);

        return $this;
    }

    protected function moveToHandledStorage(HandledStorage $toStorage, callable $conditionCallback = null, $toDirectory = '', $keepOriginalName = true, $markOriginal = true)
    {
        if (($originStorage = $this->storageManager->origin())) {
            if (is_null($conditionCallback) || $conditionCallback($originStorage)) {
                if (!$this->storageManager->exists($toStorage->getName())) {
                    $toStorage->from($originStorage->getRealPath(), $toDirectory, $keepOriginalName);
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

    public function moveToPublic($toDirectory = '', $keepOriginalName = true, $markOriginal = true)
    {
        return $this->moveToHandledStorage(
            new PublicStorage(),
            function ($originStorage) {
                return $originStorage instanceof PrivateStorage;
            },
            $toDirectory, $keepOriginalName, $markOriginal
        );
    }

    public function cloneToPublic($toDirectory = '', $keepOriginalName = true)
    {
        return $this->moveToPublic($toDirectory, $keepOriginalName, false);
    }

    public function moveToPrivate($toDirectory = '', $keepOriginalName = true, $markOriginal = true)
    {
        return $this->moveToHandledStorage(
            new PrivateStorage(),
            function ($originStorage) {
                return $originStorage instanceof PublicStorage;
            },
            $toDirectory, $keepOriginalName, $markOriginal
        );
    }

    public function cloneToPrivate($toDirectory = '', $keepOriginalName = true)
    {
        return $this->moveToPrivate($toDirectory, $keepOriginalName, false);
    }

    public function moveToCloud($toDirectory = '', $keepOriginalName = true, $markOriginal = true)
    {
        return $this->moveToHandledStorage(
            new CloudStorage(),
            function ($originStorage) {
                return $originStorage instanceof LocalStorage;
            },
            $toDirectory, $keepOriginalName, $markOriginal
        );
    }

    public function cloneToCloud($toDirectory = '', $keepOriginalName = true)
    {
        return $this->moveToCloud($toDirectory, $keepOriginalName, false);
    }

    public function moveToInline($markOriginal = true)
    {
        if (($originStorage = $this->storageManager->origin())) {
            if ($originStorage instanceof HandledStorage) {
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
        }
        return $this;
    }
}
