<?php

namespace App\Utils\ManagedFiles;

use App\Exceptions\AppException;
use App\Utils\ManagedFiles\Storage\CloudStorage;
use App\Utils\ManagedFiles\Storage\ExternalStorage;
use App\Utils\ManagedFiles\Storage\HandledStorage;
use App\Utils\ManagedFiles\Storage\LocalStorage;
use App\Utils\ManagedFiles\Storage\PrivateStorage;
use App\Utils\ManagedFiles\Storage\PublicStorage;
use Illuminate\Http\UploadedFile;

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
    public function fromExisted($file, $toDirectory = '', $keepOriginalName = true)
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
        $this->storageManager->add((new PrivateStorage())->from($file, $toDirectory, $keepOriginalName), true);

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

    protected function moveToStorage(HandledStorage $toStorage, callable $conditionCallback = null, $toDirectory = '', $keepOriginalName = true, $markOriginal = false)
    {
        $originStorage = $this->storageManager->origin();
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
        return $this;
    }

    public function moveToPublic($toDirectory = '', $keepOriginalName = true, $markOriginal = false)
    {
        return $this->moveToStorage(
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

    public function moveToPrivate($toDirectory = '', $keepOriginalName = true, $markOriginal = false)
    {
        return $this->moveToStorage(
            new PublicStorage(),
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

    public function moveToCloud($toDirectory = '', $keepOriginalName = true, $markOriginal = false)
    {
        return $this->moveToStorage(
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
}
