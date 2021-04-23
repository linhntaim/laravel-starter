<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources;

use App\Models\User;

/**
 * Class UserAccountResource
 * @package App\ModelResources
 * @mixin User
 */
class UserAccountResource extends UserResource
{
    public function toArray($request)
    {
        return $this->mergeIn([
            parent::toArray($request),
            [
                'settings' => $this->preferredSettings()->toArray(),
            ],
        ]);
    }
}
