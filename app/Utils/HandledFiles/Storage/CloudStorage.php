<?php

namespace App\Utils\HandledFiles\Storage;

use Illuminate\Support\Facades\Storage;

class CloudStorage extends HandledStorage implements IUrlStorage
{
    protected $cloud;

    public function __construct($disk = null)
    {
        parent::__construct($disk);

        $this->cloud = config('filesystems.cloud');
        $this->config = config(sprintf('filesystems.disks.%s', $this->cloud));
    }

    public function getName()
    {
        return $this->cloud;
    }

    public function setDisk($disk = null)
    {
        $this->disk = Storage::cloud();
    }
}