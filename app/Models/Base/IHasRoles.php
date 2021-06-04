<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Interface IHasRoles
 * @package App\Models\Base
 * @property string[]|array $roleNames
 * @property Role[]|Collection $roles
 */
interface IHasRoles extends IHasPermissions
{
    /**
     * @return string
     */
    public function getBelongsToManyRolesTable();

    /**
     * @return string
     */
    public function getRoleAttributeName();

    /**
     * @return string
     */
    public function getUserAttributeName();

    /**
     * @return string[]|array
     */
    public function getRoleNamesAttribute();

    /**
     * @return BelongsToMany
     */
    public function roles();
}