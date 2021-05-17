<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\ModelResources\RoleResource;
use App\Models\Base\IProtected;
use App\Models\Base\Model;
use App\ModelTraits\ProtectedTrait;
use App\Vendors\Illuminate\Support\HtmlString;
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
    use ProtectedTrait;

    public const SYSTEM = 'system';
    public const SUPER_ADMIN = 'super_admin';
    public const ADMIN = 'admin';
    // TODO: Define roles

    // TODO

    public const PROTECTED = [
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
        'html_description',
    ];

    protected $appends = [
        'html_description',
    ];

    protected $resourceClass = RoleResource::class;

    public static function getProtectedKey()
    {
        return 'name';
    }

    public function getHtmlDescriptionAttribute()
    {
        return (new HtmlString($this->description))->escape()->break()->toHtml();
    }

    public function getPermissionNamesAttribute()
    {
        static $permissionNames = null;
        if (is_null($permissionNames)) {
            $permissionNames = $this->permissions->pluck('name')->all();
        }
        return $permissionNames;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'roles_users', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permissions_roles', 'role_id', 'permission_id');
    }

    public function toActivityLogArray()
    {
        return array_merge(parent::toActivityLogArray(), $this->toActivityLogArrayFrom([
            'permission_names' => $this->permissionNames,
        ]));
    }

    // TODO：

    // TODO
}
