<?php

namespace App\Models\Base;

/**
 * Interface IUserVerifyEmail
 * @package App\Models\Base
 * @property string $email_verified_code
 * @property string $email_verified_at
 * @property bool $emailVerified
 */
interface IUserVerifyEmail
{
    /**
     * @return string
     */
    public function getEmailVerifiedCodeAttributeName();

    /**
     * @return string
     */
    public function getEmailVerifiedAtAttributeName();

    /**
     * @return string
     */
    public function getEmailVerifiedAttributeName();

    /**
     * @return string
     */
    public function getEmailForVerification();

    /**
     * @return bool
     */
    public function hasVerifiedEmail();

    /**
     * @return bool
     */
    public function getEmailVerifiedAttribute();

    /**
     * @param bool $value
     */
    public function setEmailVerifiedAttribute($value);

    /**
     * @return string|null
     */
    public function getEmailVerificationExpiredAt();

    /**
     * @return void
     */
    public function sendEmailVerificationNotification();
}