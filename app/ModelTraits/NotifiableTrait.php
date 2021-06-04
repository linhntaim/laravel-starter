<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

use Illuminate\Notifications\Notifiable;

trait NotifiableTrait
{
    use Notifiable, NotificationTrait {
        NotificationTrait::notifications insteadof Notifiable;
    }
}
