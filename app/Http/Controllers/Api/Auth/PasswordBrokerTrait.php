<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Auth;

use Closure;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Password;

trait PasswordBrokerTrait
{
    /**
     * @return PasswordBroker
     */
    protected function broker()
    {
        return Password::broker();
    }

    protected function brokerGetUser(array $credentials)
    {
        return $this->broker()->getUser($credentials);
    }

    protected function brokerTokenExists(CanResetPassword $user, $token)
    {
        return $this->broker()->tokenExists($user, $token);
    }

    protected function brokerSendResetLink(array $credentials, Closure $callback = null)
    {
        return $this->broker()->sendResetLink($credentials, $callback);
    }

    protected function brokerReset(array $credentials, Closure $callback)
    {
        return $this->broker()->reset($credentials, $callback);
    }
}
