<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications;

use App\Mail\AdminPasswordResetMailable;
use App\Models\Base\INotifiable;
use App\Models\Base\IUser;
use App\Utils\ClientSettings\Traits\AdminIndependentClientTrait;

class AdminPasswordResetNotification extends PasswordResetNotification
{
    use AdminIndependentClientTrait;

    /**
     * @param INotifiable|IUser $notifiable
     * @return AdminPasswordResetMailable
     */
    protected function getMailable($notifiable)
    {
        return new AdminPasswordResetMailable();
    }

    // TODO:

    // TODO
}
