<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

use App\Models\Base\IUser;
use App\Models\User;

/**
 * Interface IUserRepository
 * @package App\ModelRepositories\Base
 */
interface IUserRepository extends IProtectedRepository
{
    /**
     * @param User|IUser|mixed|null $id
     * @return IUser|mixed|null
     */
    public function model($id = null);

    /**
     * @param bool $pinned
     * @return IUser|mixed
     */
    public function newModel($pinned = true);

    /**
     * @param array $attributes
     * @param array $userAttributes
     * @param array $userSocialAttributes
     * @return IUser|mixed
     */
    public function createWithAttributesFromSocial(array $attributes = [], array $userAttributes = [], array $userSocialAttributes = []);

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

    /**
     * @return IUser|mixed
     */
    public function updateLastAccessedAt();
}
