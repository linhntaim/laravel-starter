<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Models\Base\IProtected;
use App\Models\Base\Model;
use App\ModelTraits\ProtectedTrait;

/**
 * Class Permission
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $description
 */
class Permission extends Model implements IProtected
{
    use ProtectedTrait;

    const BE_SYSTEM = 'be-system';
    const BE_SUPER_ADMIN = 'be-super-admin';
    const IMPERSONATE = 'impersonate';
    const ROLE_MANAGE = 'role-manage';
    const ADMIN_MANAGE = 'admin-manage';
    const ACTIVITY_MANAGE = 'activity-log-manage';
    // TODO: Define perms

    // TODO

    const PROTECTED = [
        Permission::BE_SYSTEM,
        Permission::BE_SUPER_ADMIN,
        Permission::IMPERSONATE,
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

    public static function getProtectedKey()
    {
        return 'name';
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permissions_roles', 'permission_id', 'role_id');
    }
}
