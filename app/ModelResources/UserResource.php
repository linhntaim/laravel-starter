<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources;

use App\ModelResources\Base\ModelResource;
use App\ModelResources\Base\ModelTransformTrait;
use App\Models\User;
use App\Utils\SocialLogin;

/**
 * Class UserResource
 * @package App\ModelResources
 * @mixin User
 */
class UserResource extends ModelResource
{
    use ModelTransformTrait;

    public function toArray($request)
    {
        return $this->mergeInWithCurrentArray($request, [
            $this->mergeWhen(SocialLogin::getInstance()->enabled(), [
                'socials' => $this->modelTransform($this->whenLoaded('socials'), $request),
            ]),
        ]);
    }
}
