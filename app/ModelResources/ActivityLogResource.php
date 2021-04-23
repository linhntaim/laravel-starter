<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources;

use App\ModelResources\Base\ModelResource;
use App\ModelResources\Base\ModelTransformTrait;
use App\Models\ActivityLog;

/**
 * Class ActivityLogResource
 * @package App\ModelResources
 * @mixin ActivityLog
 */
class ActivityLogResource extends ModelResource
{
    use ModelTransformTrait;

    public function toArray($request)
    {
        return $this->mergeInWithCurrentArray($request, [
            [
                'screens' => $this->screens_array_value,
                'payload' => $this->payload_array_value,
                'device' => $this->modelTransform($this->whenLoaded('device'), $request),
            ],
        ]);
    }
}
