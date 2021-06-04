<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events;

use App\Models\Base\IUser;
use Illuminate\Auth\Events\PasswordReset;

/**
 * Class PasswordResetEvent
 * @package App\Events
 * @property IUser $user
 */
class PasswordResetEvent extends PasswordReset
{
    /**
     * @var string
     */
    public $password;

    public function __construct($user, $password)
    {
        parent::__construct($user);

        $this->password = $password;
    }
}