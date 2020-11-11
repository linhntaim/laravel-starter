<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\ModelResources\RoleResource;
use App\Models\Base\IProtected;
use App\Models\Base\Model;
use App\ModelTraits\MemorizeTrait;
use App\ModelTraits\ProtectedTrait;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Role
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $description
 * @property string[]|array $permissionNames
 * @property Permission[]|Collection $permissions
 */
class Role extends Model implements IProtected
{
    use MemorizeTrait, ProtectedTrait;

    const SYSTEM = 'system';
    const SUPER_ADMIN = 'super_admin';
    const ADMIN = 'admin';
    // TODO: Define roles

    // TODO

    const PROTECTED = [
        Role::SYSTEM,
        Role::SUPER_ADMIN,
        // TODO: Protected roles

        // TODO
    ];

    protected $table = 'roles';

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

    protected $resourceClass = RoleResource::class;

    public static function getProtectedKey()
    {
        return 'name';
    }

    public function getPermissionNamesAttribute()
    {
        return $this->remind('permission_names', function () {
            return $this->permissions->pluck('name')->all();
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'roles_users', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permissions_roles', 'role_id', 'permission_id');
    }

    public function toActivityLogArray($except = [])
    {
        return array_merge(parent::toActivityLogArray($except), $this->toActivityLogArrayFrom([
            'permission_names' => $this->permissionNames,
        ]));
    }

    // TODO：

    // TODO
}
