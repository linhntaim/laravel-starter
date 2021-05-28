<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Vendors\Illuminate\Auth\Passwords;

use Illuminate\Auth\Passwords\DatabaseTokenRepository as BaseDatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Carbon;

class DatabaseTokenRepository extends BaseDatabaseTokenRepository
{
    private function tokenExists($token)
    {
        return $this->getTable()
                ->where('token', $token)
                ->count() > 0;
    }

    protected function tokenExpired($createdAt)
    {
        return $this->expires && parent::tokenExpired($createdAt);
    }

    public function createNewToken()
    {
        // Create unique token
        while (($token = parent::createNewToken()) && $this->tokenExists($token)) {
        }
        return $token;
    }

    public function getPayload($email, $token)
    {
        // Do not hash the token anymore
        return ['email' => $email, 'token' => $token, 'created_at' => new Carbon];
    }

    public function exists(CanResetPasswordContract $user, $token)
    {
        $record = (array)$this->getTable()->where(
            'email', $user->getEmailForPasswordReset()
        )->first();

        return $record
            && !$this->tokenExpired($record['created_at'])
            && $token == $record['token']; // Compare the value, not check by hashing anymore
    }
}
