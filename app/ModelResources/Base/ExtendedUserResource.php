<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources\Base;

use App\Models\Base\ExtendedUserModel;

/**
 * Class ExtendedUserResource
 * @package App\ModelResources
 * @mixin ExtendedUserModel
 */
abstract class ExtendedUserResource extends ModelResource
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