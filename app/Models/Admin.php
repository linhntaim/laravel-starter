<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\ModelResources\AdminResource;
use App\Models\Base\ExtendedUserModel;
use App\Models\Base\IHasRole;
use App\ModelTraits\HasRoleTrait;
use App\Notifications\AdminEmailVerificationNotification;
use App\Notifications\AdminPasswordResetNotification;

/**
 * Class Admin
 * @package App\Models
 * @property int $user_id
 * @property string $display_name
 * @property string $avatarUrl
 * @property User $user
 * @property HandledFile $avatar
 */
class Admin extends ExtendedUserModel implements IHasRole
{
    use HasRoleTrait {
        HasRoleTrait::modelConstruct as userHasRoleConstruct;
    }

    public const MAX_AVATAR_SIZE = 512;

    protected $table = 'admins';

    protected $fillable = [
        'user_id',
        'avatar_id',
        'display_name',
    ];

    protected $visible = [
        'user_id',
        'display_name',
        'avatar_url',
    ];

    protected $appends = [
        'avatar_url',
    ];

    protected $activityLogHidden = [
        'user_id',
    ];

    protected $resourceClass = AdminResource::class;

    public function __construct(array $attributes = [])
    {
        $this->userHasRoleConstruct();

        parent::__construct($attributes);
    }

    #region Get Attributes
    public function getAvatarUrlAttribute()
    {
        return is_null($this->attributes['avatar_id']) ? null : $this->avatar->url;
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

    protected function getPasswordResetNotificationClass()
    {
        return AdminPasswordResetNotification::class;
    }

    protected function getEmailVerificationNotificationClass()
    {
        return AdminEmailVerificationNotification::class;
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
