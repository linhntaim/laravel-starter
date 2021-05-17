<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     * @return string[]|array
     */
    public function getPermissionNamesAttribute();

    /**
     * @return BelongsToMany
     */
    public function roles();
}