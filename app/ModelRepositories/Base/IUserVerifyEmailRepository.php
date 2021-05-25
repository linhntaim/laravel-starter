<?php

namespace App\ModelRepositories\Base;

interface IUserVerifyEmailRepository
{
    public function notifyEmailVerification($again = false);

    public function verifyEmailByCode($code);
}