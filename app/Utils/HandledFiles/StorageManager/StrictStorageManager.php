<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\StorageManager;

use App\Exceptions\AppException;
use App\Utils\HandledFiles\Storage\Storage;

class StrictStorageManager extends StorageManager
{
    /**
     * @param Storage $storage
     * @param bool $markOriginal
     * @return StorageManager
     * @throws
     */
    public function add(Storage $storage, $markOriginal = false)
    {
        if ($this->exists($storage->getName())) {
            throw new AppException('Storage has been existed');
        }

        return parent::add($storage, $markOriginal);
    }
}
