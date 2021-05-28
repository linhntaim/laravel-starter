<?php

namespace App\Models\Base;

/**
 * Interface IUserHasPermissions
 * @package App\Models\Base
 * @property string[]|array $permissionNames
 */
interface IUserHasPermissions
{
    /**
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission(string $permissionName);

    /**
     * @param string[]|array $permissionNames
     * @return bool
     */
    public function hasPermissions(array $permissionNames);

    /**
     * @param string[]|array $permissionNames
     * @return bool
     */
    public function hasPermissionsAll(array $permissionNames);

    /**
     * @return string[]|array
     */
    public function getPermissionNamesAttribute();
}