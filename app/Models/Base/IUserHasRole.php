<?php

namespace App\Models\Base;

use App\Models\Role;

interface IUserHasRole
{
    /**
     * @return string
     */
    public function getRoleAttributeName();

    /**
     * @return string|null
     */
    public function getRoleNameAttribute();

    /**
     * @return string[]|array|null
     */
    public function getPermissionNamesAttribute();
}