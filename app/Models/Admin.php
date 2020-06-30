<?php

namespace App\Models;

use App\ModelTraits\MemorizeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Admin
 * @package App\Models
 * @property User $user
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
