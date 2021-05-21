<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

/**
 * Interface IUser
 * @package App\Models\Base
 */
interface IUser extends IContactable, ILocalizable, IProtected, INotifiable, INotifier
{
    public function getId();

    public function getEmailForPasswordReset();

    public function getPasswordMinLength();

    public function getPasswordResetExpiredAt();

    public function sendPasswordResetNotification($token);
}
