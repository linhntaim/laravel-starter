<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\ModelResources\RoleResource;
use App\Models\Base\Model;
use App\ModelTraits\MemorizeTrait;
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
class Role extends Model
{
    use MemorizeTrait;

    const SYSTEM = 1;
    const SUPER_ADMIN = 2;
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

    public function scopeNoneProtected($query)
    {
        return $query->whereNotIn('id', static::PROTECTED);
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
