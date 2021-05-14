<?php

namespace App\ModelTraits;

use App\Models\Permission;
use App\Models\Role;

/**
 * Trait UserHasRoleTrait
 * @package App\ModelTraits
 * @property Role|null $role
 */
trait UserHasRoleTrait
{
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
                $permissionNames = $this->role->permissions
                    ->map(function (Permission $permission) {
                        return $permission->name;
                    })
                    ->unique()
                    ->all();
            }
        }
        return $permissionNames;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, $this->getRoleAttributeName());
    }
}