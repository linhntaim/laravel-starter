<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications;

use App\Mail\AdminEmailVerificationMailable;
use App\Models\Base\INotifiable;
use App\Models\Base\IUser;
use App\Models\Base\IHasEmailVerified;
use App\Utils\ClientSettings\Traits\AdminIndependentClientTrait;

class AdminEmailVerificationNotification extends EmailVerificationNotification
{
    use AdminIndependentClientTrait;

    /**
     * @param INotifiable|IUser|IHasEmailVerified $notifiable
     * @return AdminEmailVerificationMailable
     */
    protected function getMailable($notifiable)
    {
        return new AdminEmailVerificationMailable();
    }

    // TODO:

    // TODO
}
