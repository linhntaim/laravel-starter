<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

use App\Configuration;
use App\Models\DatabaseNotification;
use App\Models\User;
use App\ModelTraits\MemorizeTrait;
use App\ModelTraits\NotificationTrait;
use App\ModelTraits\ProtectedTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * Trait UserExtendedTrait
 * @package App\ModelTraits
 * @property int $user_id
 * @property string $username
 * @property string $email
 * @property User $user
 * @property DatabaseNotification[] $notifications
 */
abstract class ExtendedUserModel extends Model implements IUser
{
    use Notifiable, NotificationTrait {
        NotificationTrait::notifications insteadof Notifiable;
    }
    use CanResetPassword, MemorizeTrait, ProtectedTrait, SoftDeletes;

    const PROTECTED = User::PROTECTED;

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    public function getPasswordMinLength()
    {
        return Configuration::PASSWORD_MIN_LENGTH;
    }

    public static function getProtectedKey()
    {
        return 'user_id';
    }

    public function getUsernameAttribute()
    {
        return $this->user->username;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getEmailForPasswordReset()
    {
        return $this->preferredEmail();
    }

    public function getId()
    {
        return $this->getKey();
    }

    public function preferredName()
    {
        return $this->user->preferredName();
    }

    public function preferredEmail()
    {
        return $this->user->preferredEmail();
    }

    public function preferredAvatarUrl()
    {
        return $this->user->preferredAvatarUrl();
    }

    public function preferredLocale()
    {
        return $this->user->preferredLocale();
    }

    public function preferredSettings()
    {
        return $this->user->preferredSettings();
    }

    public function getPasswordResetExpiredAt()
    {
        return $this->user->getPasswordResetExpiredAt();
    }
}
