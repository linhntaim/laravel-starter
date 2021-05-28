<?php

namespace App\ModelRepositories\Base;

interface IUserVerifyEmailRepository
{
    public function notifyEmailVerification($again = false);

    public function verifyEmailByCode($code);

    public function verifyEmail();

    public function unverifyEmailByCode($code, $fresh = true);

    public function unverifyEmail($fresh = true);
}