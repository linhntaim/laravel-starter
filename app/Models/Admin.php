<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\ModelResources\AdminResource;
use App\Models\Base\ExtendedUserModel;
use App\Notifications\AdminResetPasswordNotification;

/**
 * Class Admin
 * @package App\Models
 * @property int $user_id
 * @property string $display_name
 * @property string $roleName
 * @property string $avatarUrl
 * @property string[] $permissionNames
 * @property User $user
 * @property Role $role
 * @property HandledFile $avatar
 */
class Admin extends ExtendedUserModel
{
    public const MAX_AVATAR_SIZE = 512;

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

    protected $activityLogHidden = [
        'user_id',
    ];

    protected $resourceClass = AdminResource::class;

    #region Get Attributes
    public function getRoleAttribute()
    {
        return $this->remind('role', function () {
            return $this->role()->with('permissions')->first();
        }, function ($memorizedRole) {
            return $memorizedRole->id == $this->attributes['role_id'];
        });
    }

    public function getRoleNameAttribute()
    {
        $role = $this->role;
        return empty($role) ? null : $this->role->name;
    }

    public function getPermissionNamesAttribute()
    {
        return $this->remind('permission_names', function () {
            $role = $this->role;
            $permissionNames = null;
            if (!empty($role)) {
                $permissionNames = [];
                $role->permissions->each(function (Permission $permission) use (&$permissionNames) {
                    if (!in_array($permission->name, $permissionNames)) {
                        $permissionNames[] = $permission->name;
                    }
                });
            }
            return $permissionNames;
        });
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

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }

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

    public function toActivityLogArray()
    {
        return array_merge($this->user->toActivityLogArray(), parent::toActivityLogArray());
    }
    #endregion

    // TODO:

    // TODO
}
