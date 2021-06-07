<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

use App\Models\Base\IUser;

interface IHasPasswordRepository
{
    /**
     * @param string $password
     * @return IUser|mixed
     */
    public function updatePassword($password);

    /**
     * @param string $password
     * @return IUser|mixed
     */
    public function updatePasswordRandomly(&$password);
}