<?php

namespace App\ModelTraits;

use App\Models\Role;

/**
 * Trait UserHasRoleTrait
 * @package App\ModelTraits
 * @property Role|null $role
 * @property string[]|array $permissionNames
 */
trait UserHasRoleTrait
{
    use UserHasPermissions;

    public function getRoleAttributeName()
    {
        return 'role_id';
    }

    public function getRoleNameAttribute()
    {
        return optional($this->role)->name;
    }

    public function getPermissionNamesAttribute()
    {
        static $permissionNames = null;
        if (is_null($permissionNames)) {
            if (is_null($this->role)) {
                $permissionNames = [];
            }
            else {
                $permissionNames = $this->role->permissionNames;
            }
        }
        return $permissionNames;
    }

    public function role()
    {
        return $this
            ->belongsTo(
                Role::class,
                $this->getRoleAttributeName()
            )
            ->with('permissions');
    }
}