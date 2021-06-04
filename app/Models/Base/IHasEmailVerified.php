<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

/**
 * Interface IHasEmailVerified
 * @package App\Models\Base
 * @property string $email_verified_code
 * @property string $email_verified_at
 * @property bool $emailVerified
 */
interface IHasEmailVerified
{
    /**
     * @return string
     */
    public function getEmailVerifiedCodeAttributeName();

    /**
     * @return string
     */
    public function getEmailVerifiedSentAtAttributeName();

    /**
     * @return string
     */
    public function getEmailVerifiedAtAttributeName();

    /**
     * @return string
     */
    public function getEmailVerifiedAttributeName();

    /**
     * @return int
     */
    public function getEmailVerifiedCodeLength();

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
     * @return bool
     */
    public function getEmailVerificationExpired();

    /**
     * @return void
     */
    public function sendEmailVerificationNotification();
}