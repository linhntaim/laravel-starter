<?php

namespace App\Utils\ManagedFiles;

use App\Utils\ManagedFiles\Storage\Storage;

abstract class StorageManager
{
    protected $storage;
    protected $origin;

    public function __construct()
    {
        $this->storage = collect([]);
    }

    public function stored()
    {
        return $this->storage->count() > 0;
    }

    /**
     * @return Storage
     */
    public function origin()
    {
        return $this->storage[$this->origin]['storage'];
    }

    public function removeOrigin()
    {
        $this->storage->splice($this->origin, 1);
        return $this;
    }

    public function add(Storage $storage, $markOriginal = false)
    {
        $this->storage->push([
            'name' => $storage->getName(),
            'storage' => $storage,
        ]);

        if ($markOriginal) {
            $this->origin = $this->storage->count() - 1;
        }

        return $this;
    }

    public function getBy($name, $first = true)
    {
        $get = $this->storage->where('name', '=', $name);
        return $first ? $get->first : $get;
    }

    /**
     * @param Storage|string $storage
     * @return bool
     */
    public function exists($storage)
    {
        return $this->storage->contains('name', '=', $storage instanceof Storage ? $storage->getName() : $storage);
    }
}
