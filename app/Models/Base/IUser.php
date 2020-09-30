<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

interface IUser
{
    public function preferredEmail();

    public function preferredName();

    public function preferredAvatarUrl();

    public function preferredLocale();

    public function preferredSettings();

    public function getPasswordResetExpiredAt();
}
