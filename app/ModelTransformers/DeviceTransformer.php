<?php

namespace App\ModelTransformers;

class DeviceTransformer extends ModelTransformer
{
    public function toArray()
    {
        $device = $this->getModel();
        return [
            'provider' => $device->provider,
            'secret' => $device->secret,
        ];
    }
}
