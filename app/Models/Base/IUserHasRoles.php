<?php

namespace App\Models\Base;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Interface IUserHasRoles
 * @package App\Models\Base
 * @property string[]|array $roleNames
 * @property Role[]|Collection $roles
 */
interface IUserHasRoles extends IUserHasPermissions
{
    /**
     * @return string
     */
    public function getRoleAttributeName();

    /**
     * @return string[]|array
     */
    public function getRoleNamesAttribute();

    /**
     * @return BelongsToMany
     */
    public function roles();
}