<?php

namespace App\Utils\ManagedFiles;

use App\Exceptions\AppException;
use App\Utils\ManagedFiles\Storage\ExternalStorage;
use App\Utils\ManagedFiles\Storage\LocalStorage;
use App\Utils\ManagedFiles\Storage\PublicStorage;
use Illuminate\Http\UploadedFile;

class Filer
{
    /**
     * @var StorageManager
     */
    protected $storageManager;

    protected $name;
    protected $type;
    protected $size;

    public function __construct()
    {
        $this->storageManager = new StrictStorageManager();
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

        $storage = new ExternalStorage($url);
        $this->name = $storage->getFilename();
        $this->storageManager->add($storage, true);

        return $this;
    }

    public function fromUploaded(UploadedFile $uploadedFile)
    {
        $this->checkIfCanInitializeFromAnySource();

        $storage = (new LocalStorage())->fromUploaded($uploadedFile);
        $this->name = $uploadedFile->getClientOriginalName();
        $this->storageManager->add($storage, true);

        return $this;
    }

    public function moveToPublic()
    {
        $originStore = $this->storageManager->origin();
        if ($originStore instanceof LocalStorage) {
            if (!$this->storageManager->exists(PublicStorage::NAME)) {
                $storage = new PublicStorage();
                $storage->fromPath($originStore->getRealPath());

                $originStore->remove();
                $this->storageManager->removeOrigin()
                    ->add($storage, true);
            }
        }

        return $this;
    }

    public function moveToLocal()
    {
        $originStore = $this->storageManager->origin();
        if ($originStore instanceof PublicStorage) {
            if (!$this->storageManager->exists(LocalStorage::NAME)) {
                $storage = new LocalStorage();
                $storage->fromPath($originStore->getRealPath());

                $originStore->remove();
                $this->storageManager->removeOrigin()
                    ->add($storage, true);
            }
        }

        return $this;
    }
}
