<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Configuration;
use App\ModelResources\UserResource;
use App\Models\Base\IModel;
use App\Models\Base\IUser;
use App\Models\Base\IUserHasSettings;
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
class User extends Authenticatable implements IModel, IUser
{
    use HasApiTokens;
    use ModelTrait, UserTrait, PassportTrait;

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
        'remember_token',
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

    public function getPasswordMinLength()
    {
        return Configuration::PASSWORD_MIN_LENGTH;
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

    public function scopeNoneProtected($query)
    {
        return $query->whereNotIn('id', static::PROTECTED);
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

    #region HasLocalePreference
    public function preferredLocale()
    {
        return $this->preferredSettings()->getLocale();
    }

    #endregion

    public function getId()
    {
        return $this->getKey();
    }

    public function preferredEmail()
    {
        return $this->email;
    }

    public function preferredName()
    {
        return isset($this->attributes['username']) ?
            $this->attributes['username']
            : Str::before($this->preferredEmail(), '@');
    }

    public function preferredAvatarUrl()
    {
        return null;
    }

    public function preferredSettings()
    {
        return $this instanceof IUserHasSettings ? $this->getSettings() : Facade::capture();
    }

    public function getPasswordResetExpiredAt()
    {
        $passwordReset = $this->passwordReset;
        return empty($passwordReset) ? null : $passwordReset->sdStExpiredAt;
    }
}
