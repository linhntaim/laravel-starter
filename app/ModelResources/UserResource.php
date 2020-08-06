<?php

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
            SocialLogin::getInstance()->enabled() ? $this->merge([
                'socials' => $this->modelTransform($this->whenLoaded('socials'), $request),
            ]) : null,
        ];
    }
}
