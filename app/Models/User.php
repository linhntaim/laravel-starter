<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\ModelResources\UserResource;
use App\Models\Base\IHasEmailVerified;
use App\Models\Base\IHasSettings;
use App\Models\Base\IModel;
use App\Models\Base\IUser;
use App\ModelTraits\ModelTrait;
use App\ModelTraits\PassportTrait;
use App\ModelTraits\UserTrait;
use App\Utils\ClientSettings\Facade;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property bool $hasPassword
 * @property PasswordReset $passwordReset
 */
class User extends Authenticatable implements IModel, IUser, IHasEmailVerified
{
    use HasApiTokens;
    use ModelTrait, PassportTrait;
    use UserTrait {
        modelConstruct as userConstruct;
    }

    public const USER_SYSTEM_ID = 1;
    public const USER_SUPER_ADMINISTRATOR_ID = 2;
    public const USER_ADMINISTRATOR_ID = 3;
    public const PROTECTED = [
        User::USER_SYSTEM_ID,
        User::USER_SUPER_ADMINISTRATOR_ID,
        User::USER_ADMINISTRATOR_ID,
    ];

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'password_changed_at',
        'last_accessed_at',
    ];

    protected $visible = [
        'id',
        'email',
        'username',
        'has_password',
        'ts_last_accessed_at',
        'sd_st_last_accessed_at',
    ];

    protected $appends = [
        'has_password',
        'ts_last_accessed_at',
        'sd_st_last_accessed_at',
    ];

    protected $activityLogHidden = [

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $resourceClass = UserResource::class;

    public function __construct(array $attributes = [])
    {
        $this->userConstruct();

        parent::__construct($attributes);
    }

    public static function getProtectedKey()
    {
        return 'id';
    }

    public function getHasPasswordAttribute()
    {
        return !empty($this->attributes['password']);
    }

    public function getSdStCreatedAtAttribute()
    {
        return Facade::dateTimer()->compound(
            'shortDate',
            ' ',
            'shortTime',
            $this->attributes['created_at']
        );
    }

    public function getSdStUpdatedAtAttribute()
    {
        return Facade::dateTimer()->compound(
            'shortDate',
            ' ',
            'shortTime',
            $this->attributes['updated_at']
        );
    }

    public function getTsLastAccessedAtAttribute()
    {
        return empty($this->attributes['last_accessed_at']) ?
            null : Facade::dateTimer()->getObject($this->attributes['last_accessed_at'])->getTimestamp();
    }

    public function getSdStLastAccessedAtAttribute()
    {
        return empty($this->attributes['last_accessed_at']) ? null : Facade::dateTimer()->compound(
            'shortDate',
            ' ',
            'shortTime',
            $this->attributes['last_accessed_at']
        );
    }

    #region Relationship
    public function passwordReset()
    {
        return $this->hasOne(PasswordReset::class, 'email', 'email');
    }

    public function socials()
    {
        return $this->hasMany(UserSocial::class, 'user_id', 'id');
    }

    #endregion

    public function preferredEmail()
    {
        return $this->email;
    }

    public function preferredName()
    {
        return $this->attributes['username'] ?? Str::before($this->preferredEmail(), '@');
    }

    public function preferredAvatarUrl()
    {
        return null;
    }

    public function preferredSettings()
    {
        return $this instanceof IHasSettings ? $this->getSettings() : Facade::capture();
    }

    public function preferredLocale()
    {
        return $this->preferredSettings()->getLocale();
    }

    public function getPasswordResetExpiredAt()
    {
        $passwordReset = $this->passwordReset;
        return $passwordReset ? $passwordReset->expiredAt : null;
    }
}
