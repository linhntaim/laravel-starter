<?php

namespace App\Models;

use App\ModelTraits\IUser;
use App\ModelTraits\MemorizeTrait;
use App\ModelTraits\PassportTrait;
use App\Notifications\ResetPasswordNotification;
use App\Utils\LocalizationHelper;
use App\Utils\ClientSettings\DateTimer;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property string $display_name
 * @property string $email
 * @property PasswordReset $passwordReset
 */
class User extends Authenticatable implements HasLocalePreference, IUser
{
    use PassportTrait, HasApiTokens, Notifiable, MemorizeTrait, SoftDeletes;

    const PROTECTED = [1, 2];

    protected $table = 'users';

    protected $via = '';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getShortDateShortTimeCreatedAtAttribute()
    {
        return DateTimer::getInstance()
            ->compound(
                'shortDate',
                ' ',
                'shortTime',
                $this->attributes['created_at']
            );
    }

    public function getShortDateShortTimeUpdatedAtAttribute()
    {
        return DateTimer::getInstance()
            ->compound(
                'shortDate',
                ' ',
                'shortTime',
                $this->attributes['updated_at']
            );
    }

    public function getPasswordResetExpiredAtAttribute()
    {
        $passwordReset = $this->passwordReset;
        return empty($passwordReset) ? null : $passwordReset->sdStExpiredAt;
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
    #endregion

    #region CanResetPassword
    public function sendPasswordResetNotification($token, $fromUser = null)
    {
        $this->notify(new ResetPasswordNotification($token, $fromUser));
    }
    #endregion

    #region HasLocalePreference
    public function preferredLocale()
    {
        return $this->preferredLocalization()->getLocale();
    }
    #endregion

    public function preferredEmail()
    {
        return $this->email;
    }

    public function preferredName()
    {
        return null;
    }

    public function preferredAvatarUrl()
    {
        return null;
    }

    public function preferredLocalization()
    {
        return LocalizationHelper::getInstance();
    }
}
