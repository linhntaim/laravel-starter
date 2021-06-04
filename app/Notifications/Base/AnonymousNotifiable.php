<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

use App\Models\Base\INotifiable;
use Illuminate\Notifications\AnonymousNotifiable as BaseAnonymousNotifiable;

class AnonymousNotifiable extends BaseAnonymousNotifiable implements INotifiable
{
    public function routeNotificationFor($driver, $notification = null)
    {
        return parent::routeNotificationFor($driver);
    }
}