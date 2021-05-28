<?php

namespace App\ModelTraits;

use App\Notifications\EmailVerificationNotification;
use App\Utils\ClientSettings\DateTimer;
use Carbon\Carbon;

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
        return $this->mergeFillable([
            $this->getEmailVerifiedCodeAttributeName(),
            $this->getEmailVerifiedSentAtAttributeName(),
            $this->getEmailVerifiedAtAttributeName(),
            $this->getEmailVerifiedAttributeName(),
        ])->mergeAppends([
            $this->getEmailVerifiedAttributeName(),
        ]);
    }

    public function getEmailVerifiedCodeAttributeName()
    {
        return 'email_verified_code';
    }

    public function getEmailVerifiedSentAtAttributeName()
    {
        return 'email_verified_sent_at';
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
     * @return int
     */
    public function getEmailVerifiedCodeLength()
    {
        return 32;
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
        return ($expire = config('auth.verification.expire'))
        && !is_null($emailVerifiedSentAt = $this->attributes[$this->getEmailVerifiedSentAtAttributeName()]) ?
            Carbon::parse($emailVerifiedSentAt)
                ->addMinutes($expire)
                ->format(DateTimer::DATABASE_FORMAT)
            : null;
    }

    /**
     * @return bool
     */
    public function getEmailVerificationExpired()
    {
        return !is_null($expiredAt = $this->getEmailVerificationExpiredAt())
            && $expiredAt <= DateTimer::syncNow();
    }

    /**
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify($this->getEmailVerificationNotification());
    }

    /**
     * @return EmailVerificationNotification
     */
    protected function getEmailVerificationNotification()
    {
        $notificationClass = $this->getEmailVerificationNotificationClass();
        return new $notificationClass();
    }

    protected function getEmailVerificationNotificationClass()
    {
        return EmailVerificationNotification::class;
    }
}