<?php

namespace App\ModelResources;

use App\Models\User;

/**
 * Class UserAccountResource
 * @package App\ModelResources
 * @mixin User
 */
class UserAccountResource extends UserResource
{
    public function toCustomArray($request)
    {
        return [
            $this->merge(parent::toCustomArray($request)),
            $this->merge([
                'settings' => $this->preferredSettings()->toArray(),
            ]),
        ];
    }
}
