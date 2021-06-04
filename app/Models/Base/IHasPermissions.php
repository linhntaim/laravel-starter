<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

/**
 * Interface IHasPermissions
 * @package App\Models\Base
 * @property string[]|array $permissionNames
 */
interface IHasPermissions
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