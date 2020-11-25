<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

use App\Models\DatabaseNotification;
use App\Models\User;
use App\ModelTraits\MemorizeTrait;
use App\ModelTraits\NotificationTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * Trait UserExtendedTrait
 * @package App\ModelTraits
 * @property User $user
 * @property DatabaseNotification[] $notifications
 */
abstract class ExtendedUserModel extends Model implements IUser
{
    use Notifiable, NotificationTrait {
        NotificationTrait::notifications insteadof Notifiable;
    }
    use CanResetPassword, MemorizeTrait, SoftDeletes;

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getEmailForPasswordReset()
    {
        return $this->preferredEmail();
    }

    public function preferredEmail()
    {
        return $this->user->preferredEmail();
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
