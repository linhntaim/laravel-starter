<?php

namespace App\ModelTraits;

trait UserHasPermissions
{
    public function getPermissionNamesAttributeName()
    {
        return 'permission_names';
    }

    public function hasPermission(string $permissionName)
    {
        return in_array($permissionName, $this->permissionNames);
    }

    public function hasPermissions(array $permissionNames)
    {
        foreach ($permissionNames as $permissionName) {
            if ($this->hasPermission($permissionName)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermissionsAll(array $permissionNames)
    {
        foreach ($permissionNames as $permissionName) {
            if (!$this->hasPermission($permissionName)) {
                return false;
            }
        }
        return true;
    }
}