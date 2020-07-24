<?php

namespace App\Utils\HandledFiles\StorageManager;

use App\Utils\HandledFiles\Storage\HandledStorage;
use App\Utils\HandledFiles\Storage\Storage;

abstract class StorageManager
{
    protected $storage;
    protected $origin;

    public function __construct()
    {
        $this->storage = collect([]);
    }

    public function clear()
    {
        while ($this->storage->offsetExists(0)) {
            $this->storage->pop();
        }
    }

    public function stored()
    {
        return $this->storage->count() > 0;
    }

    public function each(callable $callback)
    {
        foreach ($this->storage as $storage) {
            $callback($storage['name'], $storage['storage']);
        }
        return $this;
    }

    /**
     * @return Storage
     */
    public function origin()
    {
        return $this->stored() && !is_null($this->origin) ? $this->storage->offsetGet($this->origin)['storage'] : null;
    }

    public function originSize()
    {
        if (($origin = $this->origin()) && $origin instanceof HandledStorage) {
            return $origin->getSize();
        }
        return -1;
    }

    public function originMime()
    {
        if (($origin = $this->origin()) && $origin instanceof HandledStorage) {
            return $origin->getMime();
        }
        return -1;
    }

    public function removeOrigin()
    {
        $this->storage->splice($this->origin, 1);
        $this->origin = null;
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
