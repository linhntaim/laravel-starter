<?php

namespace App\Models;

use App\ModelResources\AdminResource;
use App\Models\Base\ExtendedUserModel;
use App\Utils\ConfigHelper;

/**
 * Class Admin
 * @package App\Models
 * @property string $display_name
 * @property string $roleName
 * @property string $avatarUrl
 * @property string[] $permissionNames
 * @property User $user
 * @property Role $role
 * @property ManagedFile $avatar
 */
class Admin extends ExtendedUserModel
{
    const MAX_AVATAR_SIZE = 512;

    protected $table = 'admins';

    protected $fillable = [
        'user_id',
        'role_id',
        'avatar_id',
        'display_name',
    ];

    protected $visible = [
        'user_id',
        'display_name',
        'role_name',
        'permission_names',
        'avatar_url',
    ];

    protected $appends = [
        'role_name',
        'permission_names',
        'avatar_url',
    ];

    protected $resourceClass = AdminResource::class;

    #region Get Attributes
    public function getRoleAttribute()
    {
        if (!$this->memorized('role') || $this->remind('role')->id != $this->attributes['role_id']) {
            $role = $this->role()->first();
            if (!empty($role)) {
                $role->load('permissions');
            }
            $this->memorize('role', $role);
        }
        return $this->remind('role');
    }

    public function getRoleNameAttribute()
    {
        $role = $this->role;
        return empty($role) ? null : $this->role->name;
    }

    public function getPermissionNamesAttribute()
    {
        if (!$this->memorized('permission_names')) {
            $role = $this->role;
            $permissionNames = null;
            if (!empty($role)) {
                $permissionNames = [];
                $this->role->permissions->each(function ($permission) use (&$permissionNames) {
                    if (!in_array($permission->name, $permissionNames)) {
                        $permissionNames[] = $permission->name;
                    }
                });
            }
            $this->memorize('permission_names', $permissionNames);
        }
        return $this->remind('permission_names');
    }

    public function getAvatarUrlAttribute()
    {
        return empty($this->attributes['avatar_id']) ? null : $this->avatar->url;
    }
    #endregion

    #region Relationships
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function avatar()
    {
        return $this->belongsTo(HandledFile::class, 'avatar_id', 'id');
    }

    #endregion

    public function preferredName()
    {
        return $this->display_name;
    }

    public function preferredAvatarUrl()
    {
        return $this->avatarUrl;
    }

    #region Functionality
    public function hasPermission($permissionName)
    {
        return in_array($permissionName, $this->permissionNames);
    }

    public function hasPermissionsAtLeast($permissionNames)
    {
        foreach ($permissionNames as $permissionName) {
            if ($this->hasPermission($permissionName)) return true;
        }
        return false;
    }

    public function hasPermissions($permissionNames)
    {
        foreach ($permissionNames as $permissionName) {
            if (!$this->hasPermission($permissionName)) return false;
        }
        return true;
    }
    #endregion
}
