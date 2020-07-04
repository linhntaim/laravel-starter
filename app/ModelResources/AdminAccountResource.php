<?php

namespace App\ModelResources;

use App\ModelResources\Base\ModelTransformTrait;
use App\Models\Admin;

/**
 * Class RoleResource
 * @package App\ModelResources
 * @mixin Admin
 */
class AdminAccountResource extends AdminResource
{
    use ModelTransformTrait;

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