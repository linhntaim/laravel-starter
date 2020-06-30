<?php

namespace App\ModelRepositories;

use App\Models\PasswordReset;

class PasswordResetRepository extends ModelRepository
{
    public function modelClass()
    {
        return PasswordReset::class;
    }

    public function getEmailByToken($token)
    {
        $passwordReset = $this->query()
            ->where('token', $token)
            ->first();
        return empty($passwordReset) ? null : $passwordReset->email;
    }
}