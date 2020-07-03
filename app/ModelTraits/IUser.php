<?php

namespace App\ModelTraits;

interface IUser
{
    public function preferredEmail();

    public function preferredName();

    public function preferredAvatarUrl();

    public function preferredLocalization();
}
