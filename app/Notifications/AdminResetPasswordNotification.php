<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications;

use App\Models\Base\IUser;
use App\Notifications\Base\AdminNowNotification;

class AdminResetPasswordNotification extends AdminNowNotification
{
    use UserResetPasswordNotificationTrait;

    protected function getMailTemplate(IUser $notifiable)
    {
        return 'admin_password_reset';
    }
}
