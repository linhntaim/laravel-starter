<?php

namespace App\Models;

use App\ModelResources\RoleResource;
use App\Models\Base\Model;

/**
 * Class Role
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $description
 * @property Permission[] permissions
 */
class Role extends Model
{
    const PROTECTED = [1, 2];

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

    public function scopeNoneProtected($query)
    {
        return $query->whereNotIn('id', static::PROTECTED);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'roles_users', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permissions_roles', 'role_id', 'permission_id');
    }
}
