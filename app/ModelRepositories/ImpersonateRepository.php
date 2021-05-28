<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\ModelRepositories\Base\ModelRepository;
use App\Models\Impersonate;

/**
 * Class ImpersonateRepository
 * @package App\ModelRepositories
 */
class ImpersonateRepository extends ModelRepository
{
    public function modelClass()
    {
        return Impersonate::class;
    }

    /**
     * @param string $impersonateToken
     * @return Impersonate
     */
    public function getByImpersonateToken($impersonateToken)
    {
        return $this->first(
            $this->query()
                ->where('impersonate_token', $impersonateToken)
        );
    }

    /**
     * @param int $userId
     * @param string $authToken
     * @return Impersonate
     */
    public function getByUserIdAndAuthToken($userId, $authToken)
    {
        return $this->first(
            $this->query()
                ->where('user_id', $userId)
                ->where('auth_token', $authToken)
        );
    }

    public function createWithAttributes(array $attributes = [])
    {
        $attributes['impersonate_token'] = $this->generateUniqueValue('impersonate_token', true, 128);
        return parent::createWithAttributes($attributes);
    }
}
