<?php

namespace App\ModelTraits;

use App\Models\Role;

/**
 * Trait UserHasRoleTrait
 * @package App\ModelTraits
 */
trait UserHasRoleTrait
{
    use UserHasPermissions;

    protected function modelConstruct()
    {
        return $this->mergeFillable([
            $this->getRoleAttributeName(),
        ])->mergeAppends([
            $this->getRoleNameAttributeName(),
            $this->getPermissionNamesAttributeName(),
        ]);
    }

    public function getRoleAttributeName()
    {
        return 'role_id';
    }

    public function getRoleNameAttributeName()
    {
        return 'role_name';
    }

    public function getRoleNameAttribute()
    {
        return optional($this->role)->name;
    }

    public function getPermissionNamesAttribute()
    {
        return $this->remind('permission_names', function () {
            if (is_null($this->role)) {
                return [];
            }
            return $this->role->permissionNames;
        });
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