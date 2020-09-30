<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources;

use App\ModelResources\Base\ModelResource;
use App\ModelResources\Base\ModelTransformTrait;
use App\Utils\SocialLogin;

class UserResource extends ModelResource
{
    use ModelTransformTrait;

    public function toCustomArray($request)
    {
        return [
            $this->merge($this->toCurrentArray($request)),
            $this->mergeWhen(SocialLogin::getInstance()->enabled(), [
                'socials' => $this->modelTransform($this->whenLoaded('socials'), $request),
            ]),
        ];
    }
}
