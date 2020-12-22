<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\ModelResources\UserResource;
use App\Models\Base\IActivityLog;
use App\Models\Base\IProtected;
use App\Models\Base\IResource;
use App\Models\Base\IUser;
use App\Models\Base\IUserHasSettings;
use App\ModelTraits\ActivityLogTrait;
use App\ModelTraits\FromModelTrait;
use App\ModelTraits\OnlyAttributesToArrayTrait;
use App\ModelTraits\MemorizeTrait;
use App\ModelTraits\NotificationTrait;
use App\ModelTraits\PassportTrait;
use App\ModelTraits\ProtectedTrait;
use App\ModelTraits\ResourceTrait;
use App\Utils\ClientSettings\Facade;
use Carbon\Carbon;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property string $display_name
 * @property string $email
 * @property bool $hasPassword
 * @property PasswordReset $passwordReset
 */
class User extends Authenticatable implements HasLocalePreference, IUser, IResource, IActivityLog, IProtected
{
    use HasFactory, Notifiable, NotificationTrait {
        NotificationTrait::notifications insteadof Notifiable;
    }
    use OnlyAttributesToArrayTrait, PassportTrait, HasApiTokens, MemorizeTrait, ResourceTrait, SoftDeletes, ActivityLogTrait, FromModelTrait, ProtectedTrait;

    const MIN_PASSWORD_LENGTH = 8;

    const USER_SYSTEM_ID = 1;
    const USER_SUPER_ADMINISTRATOR_ID = 2;
    const USER_ADMINISTRATOR_ID = 3;

    const PROTECTED = [
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
        'email',
        'username',
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

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getResourceClass()
    {
        return UserResource::class;
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

    public function preferredEmail()
    {
        return $this->email;
    }

    public function preferredName()
    {
        return Str::before($this->preferredEmail(), '@');
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
