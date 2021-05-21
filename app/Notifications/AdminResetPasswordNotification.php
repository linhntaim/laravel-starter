<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications;

use App\Mail\AdminPasswordResetMailable;
use App\Models\Base\INotifiable;
use App\Models\Base\IUser;

class AdminResetPasswordNotification extends UserResetPasswordNotification
{
    /**
     * @param INotifiable|IUser $notifiable
     * @return AdminPasswordResetMailable
     */
    protected function getMailable($notifiable)
    {
        return new AdminPasswordResetMailable();
    }
}
