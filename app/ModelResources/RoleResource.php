<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources;

use App\ModelResources\Base\ModelResource;
use App\ModelResources\Base\ModelTransformTrait;
use App\Models\Role;

/**
 * Class RoleResource
 * @package App\ModelResources
 * @mixin Role
 */
class RoleResource extends ModelResource
{
    use ModelTransformTrait;

    public function toCustomArray($request)
    {
        return [
            $this->merge($this->toCurrentArray($request)),
            $this->merge([
                'permissions' => $this->modelTransform($this->whenLoaded('permissions'), $request),
            ]),
        ];
    }
}