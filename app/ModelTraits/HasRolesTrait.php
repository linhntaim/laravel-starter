<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

use App\Models\Role;

/**
 * Trait HasRoleTrait
 * @package App\ModelTraits
 */
trait HasRolesTrait
{
    use HasPermissions;

    public function getBelongsToManyRolesTable()
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
                $this->getBelongsToManyRolesTable(),
                $this->getUserAttributeName(),
                $this->getRoleAttributeName()
            )
            ->with('permissions');
    }
}