<?php

namespace App\Utils\HandledFiles\Storage;

use App\Exceptions\AppException;
use App\Utils\ConfigHelper;
use Illuminate\Support\Facades\Storage;

class CloudStorage extends HandledStorage implements IUrlStorage
{
    protected $cloud;

    public function __construct($disk = null)
    {
        if (!ConfigHelper::get('handled_file.cloud_enabled')) {
            throw new AppException('Cloud was not enabled');
        }

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
