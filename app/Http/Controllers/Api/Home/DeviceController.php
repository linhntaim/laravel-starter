<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Api\DeviceController as BaseDeviceController;
use App\ModelResources\DeviceResource;
use App\Models\Device;

class DeviceController extends BaseDeviceController
{
    public function __construct()
    {
        parent::__construct();

        $this->setFixedModelResourceClass(
            DeviceResource::class,
            Device::class
        );
    }
}
