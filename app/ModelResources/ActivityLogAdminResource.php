<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources;

use App\Models\ActivityLog;

/**
 * Class ActivityLogAdminResource
 * @package App\ModelResources
 * @mixin ActivityLog
 */
class ActivityLogAdminResource extends ActivityLogResource
{
    public function toArray($request)
    {
        return $this->mergeIn([
            parent::toArray($request),
            [
                'admin' => $this->modelTransform($this->whenLoaded('admin'), $request),
            ],
        ]);
    }
}
