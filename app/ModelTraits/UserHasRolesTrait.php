<?php

namespace App\ModelTraits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trait UserHasRoleTrait
 * @package App\ModelTraits
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
        return $this->remind('role_names', function () {
            return $this->roles
                ->map(function (Role $role) {
                    return $role->name;
                })
                ->all();
        });
    }

    public function getPermissionNamesAttribute()
    {
        return $this->remind('permission_names', function () {
            return array_unique(
                array_merge(
                    ...$this->roles
                    ->map(function (Role $role) {
                        return $role->permissionNames;
                    })
                    ->all()
                )
            );
        });
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