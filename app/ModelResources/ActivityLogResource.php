<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources;

use App\ModelResources\Base\ModelResource;
use App\Models\ActivityLog;

/**
 * Class ActivityLogResource
 * @package App\ModelResources
 * @mixin ActivityLog
 */
class ActivityLogResource extends ModelResource
{
    public function toCustomArray($request)
    {
        return [
            $this->merge($this->toCurrentArray($request)),
            $this->merge([
                'screens' => $this->screens_array_value,
                'payload' => $this->payload_array_value,
                'device' => $this->whenLoaded('device'),
            ]),
        ];
    }
}
