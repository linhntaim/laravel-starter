<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

use Illuminate\Contracts\Auth\CanResetPassword;

/**
 * Interface IUser
 * @package App\Models\Base
 */
interface IUser extends IContactable, ILocalizable, IProtected, INotifiable, INotifier, CanResetPassword
{
    public function getId();

    public function getPasswordMinLength();

    /**
     * @return string|null
     */
    public function getPasswordResetExpiredAt();
}
