<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\ModelRepositories\Base\ModelRepository;
use App\Models\PasswordReset;

/**
 * Class PasswordResetRepository
 * @package App\ModelRepositories
 * @property  PasswordReset $model
 */
class PasswordResetRepository extends ModelRepository
{
    public function modelClass()
    {
        return PasswordReset::class;
    }

    /**
     * @param string $token
     * @return PasswordReset
     */
    public function getByToken($token)
    {
        return $this->first(
            $this->query()->where('token', $token)
        );
    }

    public function getEmailByToken($token)
    {
        $this->notStrict()->pinModel()->getByToken($token);
        return $this->doesntHaveModel() ? null : $this->model->email;
    }
}
