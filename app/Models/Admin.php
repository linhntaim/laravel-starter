<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\ModelResources\AdminResource;
use App\Models\Base\ExtendedUserModel;
use App\Models\Base\IUserHasRole;
use App\ModelTraits\UserHasRoleTrait;
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
class Admin extends ExtendedUserModel implements IUserHasRole
{
    use UserHasRoleTrait;

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
    public function getAvatarUrlAttribute()
    {
        return empty($this->attributes['avatar_id']) ? null : $this->avatar->url;
    }

    #endregion

    #region Relationships
    public function avatar()
    {
        return $this->belongsTo(HandledFile::class, 'avatar_id', 'id');
    }

    #endregion

    public function preferredName()
    {
        return $this->display_name;
    }

    public function preferredAvatarUrl()
    {
        return $this->avatarUrl;
    }

    protected function getPasswordResetNotification($token)
    {
        return new AdminResetPasswordNotification($token);
    }

    #region Functionality
    public function toActivityLogArray()
    {
        return array_merge($this->user->toActivityLogArray(), parent::toActivityLogArray());
    }
    #endregion

    // TODO:

    // TODO
}
