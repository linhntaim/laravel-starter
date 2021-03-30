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
    public function toCustomArray($request)
    {
        $array = $this->toCurrentArray($request);
        unset($array['client_agent']);
        return $array;
    }
}
