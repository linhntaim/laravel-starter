<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    const PROTECTED = [1, 2];

    protected $table = 'permissions';

    protected $fillable = [
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
