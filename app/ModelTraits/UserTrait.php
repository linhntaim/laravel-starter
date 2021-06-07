<?php

namespace App\ModelTraits;

use App\Configuration;
use App\Models\Base\IHasEmailVerified;
use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\SoftDeletes;

trait UserTrait
{
    use NotifiableTrait, NotifierTrait, ProtectedTrait, SoftDeletes;
    use HasEmailVerifiedTrait {
        modelConstruct as hasEmailVerifiedConstruct;
    }

    protected function modelConstruct()
    {
        if ($this instanceof IHasEmailVerified) {
            $this->hasEmailVerifiedConstruct();
        }
    }

    public function getId()
    {
        return $this->getKey();
    }

    public function getPasswordMinLength()
    {
        return Configuration::PASSWORD_MIN_LENGTH;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify($this->getPasswordResetNotification($token));
    }

    /**
     * @param string $token
     * @return PasswordResetNotification
     */
    protected function getPasswordResetNotification($token)
    {
        $notificationClass = $this->getPasswordResetNotificationClass();
        return new $notificationClass($token);
    }

    protected function getPasswordResetNotificationClass()
    {
        return PasswordResetNotification::class;
    }
}
