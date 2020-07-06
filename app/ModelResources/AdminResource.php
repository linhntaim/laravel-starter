<?php

namespace App\ModelResources;

use App\ModelResources\Base\ModelResource;
use App\ModelResources\Base\ModelTransformTrait;
use App\Models\Admin;

/**
 * Class RoleResource
 * @package App\ModelResources
 * @mixin Admin
 */
class AdminResource extends ModelResource
{
    use ModelTransformTrait;

    public function toCustomArray($request)
    {
        return [
            $this->merge($this->toCurrentArray($request)),
            $this->merge([
                'user' => $this->modelTransform($this->whenLoaded('user'), $request),
            ]),
        ];
    }
}