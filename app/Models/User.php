<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Utils\ConfigHelper;
use App\Utils\CryptoJs\AES;
use App\Utils\LocalizationHelper;
use App\Utils\DateTimeHelper;
use App\Utils\MemorizeTrait;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property string $display_name
 * @property string $email
 * @property OneTimeWatcher $oneTimeWatcher
 * @property MembershipWatcher $membershipWatcher
 */
class User extends Authenticatable implements HasLocalePreference
{
    use HasApiTokens, Notifiable, MemorizeTrait, SoftDeletes;

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
        'display_name',
        'password',
        'url_avatar',
        'created_at',
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
        return DateTimeHelper::getInstance()
            ->compound(
                'shortDate',
                ' ',
                'shortTime',
                $this->attributes['created_at']
            );
    }

    public function getShortDateShortTimeUpdatedAtAttribute()
    {
        return DateTimeHelper::getInstance()
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

    public function oneTimeWatcher()
    {
        return $this->hasOne(OneTimeWatcher::class, 'user_id', 'id');
    }

    public function membershipWatcher()
    {
        return $this->hasOne(MembershipWatcher::class, 'user_id', 'id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'watchers_companies', 'watcher_id', 'company_id')
            ->withPivot('department_id');
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
        return LocalizationHelper::getInstance()->getLocale();
    }

    #endregion

    public function preferredEmail()
    {
        return $this->email;
    }

    public function preferredName()
    {
        return $this->display_name;
    }

    #region Passport
    public function findForPassport($username)
    {
        if (request()->has('_e')) {
            $username = AES::decrypt($username, ConfigHelper::getClockBlockKey());
        }
        $advanced = json_decode($username);
        if ($advanced !== false) {
            if (!empty($advanced->token) && !empty($advanced->id)) {
                $sysToken = SysToken::where('type', SysToken::TYPE_LOGIN)
                    ->where('token', $advanced->token)->first();
                if (!empty($sysToken)) {
                    $sysToken->delete();
                    $user = User::where('id', $advanced->id)
                        ->orWhere('email', $advanced->id)
                        ->first();
                    if ($user) $user->via = 'token';
                    return $user;
                }
            }
        }
        return User::where('email', $username)->first();
    }

    public function validateForPassportPasswordGrant($password)
    {
        if (request()->has('_e')) {
            $password = AES::decrypt($password, ConfigHelper::getClockBlockKey());
        }
        $advanced = json_decode($password);
        if ($advanced !== false) {
            if (!empty($advanced->source) && !empty($this->via)
                && $advanced->source == $this->via) return true;
        }
        return Hash::check($password, $this->password);
    }
    #endregion
}
