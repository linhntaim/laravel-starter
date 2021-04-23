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
    protected function toCustomArray($request)
    {
        return [
            $this->merge(parent::toCustomArray($request)),
            $this->merge([
                'admin' => $this->whenLoaded('admin'),
            ]),
        ];
    }
}
