<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications;

use App\Models\Base\IUser;

class AdminResetPasswordNotification extends UserResetPasswordNotification
{
    protected function getMailTemplate(IUser $notifiable)
    {
        return 'admin_password_reset';
    }
}
