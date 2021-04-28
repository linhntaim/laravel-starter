<?php

namespace App\ModelTraits;

use Illuminate\Notifications\Notifiable;

trait NotifiableTrait
{
    use Notifiable, NotificationTrait {
        NotificationTrait::notifications insteadof Notifiable;
    }
}
