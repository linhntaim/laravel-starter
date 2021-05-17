<?php

namespace App\ModelTraits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trait UserHasRoleTrait
 * @package App\ModelTraits
 * @property Role[]|Collection $roles
 */
trait UserHasRolesTrait
{
    use UserHasPermissions;

    public function getMappingRolesTable()
    {
        return 'users_roles';
    }

    public function getRoleAttributeName()
    {
        return 'role_id';
    }

    public function getUserAttributeName()
    {
        return 'user_id';
    }

    public function getRoleNamesAttribute()
    {
        static $roleNames = null;
        if (is_null($roleNames)) {
            $roleNames = $this->roles
                ->map(function (Role $role) {
                    return $role->name;
                })
                ->all();
        }
        return $roleNames;
    }

    public function getPermissionNamesAttribute()
    {
        static $permissionNames = null;
        if (is_null($permissionNames)) {
            $permissionNames = array_unique(
                array_merge(
                    ...$this->roles
                    ->map(function (Role $role) {
                        return $role->permissionNames;
                    })
                    ->all()
                )
            );
        }
        return $permissionNames;
    }

    public function roles()
    {
        return $this
            ->belongsToMany(
                Role::class,
                $this->getMappingRolesTable(),
                $this->getUserAttributeName(),
                $this->getRoleAttributeName()
            )
            ->with('permissions');
    }
}