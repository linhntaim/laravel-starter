<?php

namespace App\Models;

use App\ModelTraits\MemorizeTrait;
use App\Utils\ConfigHelper;
use Illuminate\Database\Eloquent\Model;

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
class Admin extends Model
{
    use MemorizeTrait;

    protected $table = 'admins';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id',
        'avatar_id',
        'display_name',
    ];

    // region Get Attributes
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
        return empty($this->attributes['avatar_id']) ? ConfigHelper::defaultAvatarUrl() : $this->avatar->url;
    }
    // endregion

    // region Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function avatar()
    {
        return $this->belongsTo(ManagedFile::class, 'avatar_id', 'id');
    }
    // endregion

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
