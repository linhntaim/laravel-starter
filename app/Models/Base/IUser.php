<?php

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
