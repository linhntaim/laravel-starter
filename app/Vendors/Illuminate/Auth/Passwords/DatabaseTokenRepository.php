<?php

namespace App\Vendors\Illuminate\Auth\Passwords;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Carbon;

use Illuminate\Auth\Passwords\DatabaseTokenRepository as BaseDatabaseTokenRepository;

class DatabaseTokenRepository extends BaseDatabaseTokenRepository
{
    private function tokenExists($token)
    {
        return $this->getTable()
                ->where('token', $token)
                ->count() > 0;
    }

    public function createNewToken()
    {
        while (($token = parent::createNewToken()) && $this->tokenExists($token)) {
        }
        return $token;
    }

    public function getPayload($email, $token)
    {
        return ['email' => $email, 'token' => $token, 'created_at' => new Carbon];
    }

    public function exists(CanResetPasswordContract $user, $token)
    {
        $record = (array)$this->getTable()->where(
            'email', $user->getEmailForPasswordReset()
        )->first();

        return $record &&
            !$this->tokenExpired($record['created_at']) &&
            $token == $record['token'];
    }
}