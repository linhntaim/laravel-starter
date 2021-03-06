<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\ModelRepositories\Base\ModelRepository;
use App\Models\UserSocial;

/**
 * Class UserSocialRepository
 * @package App\ModelRepositories
 */
class UserSocialRepository extends ModelRepository
{
    public function modelClass()
    {
        return UserSocial::class;
    }

    /**
     * @param $provider
     * @param $providerId
     * @return UserSocial
     */
    public function getByProvider($provider, $providerId)
    {
        return $this->first(
            $this->query()
                ->where('provider', $provider)
                ->where('provider_id', $providerId)
        );
    }
}
