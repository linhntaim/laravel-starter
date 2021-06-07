<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Interface IHasRole
 * @package App\Models\Base
 * @property string $roleName
 * @property Role $role
 */
interface IHasRole extends IHasPermissions
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