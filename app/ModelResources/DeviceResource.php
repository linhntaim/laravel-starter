<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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
    public $hidden = [
        'client_agent',
    ];
}
