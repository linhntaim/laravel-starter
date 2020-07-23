<?php

namespace App\Utils\ManagedFiles\Storage;

use App\Exceptions\AppException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage as StorageFacade;

abstract class HandledStorage extends Storage
{
    protected $disk;

    public function __construct($disk = null)
    {
        $this->setDisk($disk);
    }

    public function setDisk($disk)
    {
        if (empty($disk)) {
            $disk = StorageFacade::disk();
        }
        if (is_string($disk)) {
            $disk = StorageFacade::disk($disk);
        }
        if (!($disk instanceof Filesystem)) {
            throw new AppException('Disk was not allowed');
        }
        $this->disk = $disk;
    }
}
