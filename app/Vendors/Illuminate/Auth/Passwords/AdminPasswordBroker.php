<?php

namespace App\Vendors\Illuminate\Auth\Passwords;

use App\ModelRepositories\AdminRepository;
use Illuminate\Auth\Passwords\PasswordBroker;

class AdminPasswordBroker extends PasswordBroker
{
    public function sendResetLink(array $credentials)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if ($this->tokens->recentlyCreatedToken($user)) {
            return static::RESET_THROTTLED;
        }

        // Once we have the reset token, we are ready to send the message out to this
        // user with a link to reset their password. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        (new AdminRepository())->model($user->id)->sendPasswordResetNotification(
            $this->tokens->create($user)
        );

        return static::RESET_LINK_SENT;
    }
}
