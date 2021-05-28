<?php

namespace App\Models\Base;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Interface IUserHasRole
 * @package App\Models\Base
 * @property string $roleName
 * @property Role $role
 */
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
     * @return BelongsTo
     */
    public function role();
}