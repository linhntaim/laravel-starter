<?php

namespace App\ModelTraits;

use App\Notifications\UserEmailVerificationNotification;
use App\Utils\ClientSettings\DateTimer;
use Illuminate\Auth\Notifications\VerifyEmail;

/**
 * Trait UserVerifyEmailTrait
 * @package App\ModelTraits
 * @property bool $email
 * @property bool $emailVerified
 */
trait UserVerifyEmailTrait
{
    protected function modelConstruct()
    {
        array_push($this->appends, ...[
            $this->getEmailVerifiedCodeAttributeName(),
            $this->getEmailVerifiedAtAttributeName(),
        ]);
        $this->appends[] = $this->getEmailVerifiedAttributeName();
        $this->visible[] = $this->getEmailVerifiedAttributeName();
    }

    public function getEmailVerifiedCodeAttributeName()
    {
        return 'email_verified_code';
    }

    public function getEmailVerifiedAtAttributeName()
    {
        return 'email_verified_at';
    }

    public function getEmailVerifiedAttributeName()
    {
        return 'email_verified';
    }

    /**
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    public function hasVerifiedEmail()
    {
        return $this->emailVerified;
    }

    /**
     * @return bool
     */
    public function getEmailVerifiedAttribute()
    {
        return !is_null($this->attributes[$this->getEmailVerifiedAtAttributeName()]);
    }

    /**
     * @param bool $value
     */
    public function setEmailVerifiedAttribute($value)
    {
        $this->attributes[$this->getEmailVerifiedAtAttributeName()] = $value ? DateTimer::syncNow() : null;
    }

    /**
     * @return string|null
     */
    public function getEmailVerificationExpiredAt()
    {
        return null;
    }

    /**
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new UserEmailVerificationNotification($this->email_verified_code));
    }
}