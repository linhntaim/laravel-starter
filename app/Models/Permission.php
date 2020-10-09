<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Models\Base\Model;

/**
 * Class Permission
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $description
 */
class Permission extends Model
{
    const BE_SYSTEM = 1;
    const BE_SUPER_ADMIN = 2;
    // TODO: Define perms

    // TODO

    const PROTECTED = [
        Permission::BE_SYSTEM,
        Permission::BE_SUPER_ADMIN,
        // TODO: Protected perms

        // TODO
    ];

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    protected $visible = [
        'id',
        'name',
        'display_name',
        'description',
    ];

    public function scopeNoneProtected($query)
    {
        return $query->whereNotIn('id', static::PROTECTED);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permissions_roles', 'permission_id', 'role_id');
    }
}
