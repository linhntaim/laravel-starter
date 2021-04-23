<?php

namespace App\ModelResources;

use App\ModelResources\Base\ModelResource;
use App\Models\Device;

/**
 * Class DeviceResource
 * @package App\ModelResources\Home
 * @mixin Device
 */
class DeviceResource extends ModelResource
{
    protected $hidden = [
        'client_agent',
    ];
}
