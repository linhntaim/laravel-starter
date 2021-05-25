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
        $this->fillable[] = $this->getRoleAttributeName();
        $hasRoleAttributeNames = [
            $this->getRoleNameAttributeName(),
            $this->getPermissionNamesAttributeName(),
        ];
        array_push($this->appends, ...$hasRoleAttributeNames);
        array_push($this->visible, ...$hasRoleAttributeNames);
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