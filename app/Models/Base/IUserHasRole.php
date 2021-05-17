<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface IUserHasRole extends IUserHasPermissions
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
     * @return string[]|array
     */
    public function getPermissionNamesAttribute();

    /**
     * @return BelongsTo
     */
    public function role();
}