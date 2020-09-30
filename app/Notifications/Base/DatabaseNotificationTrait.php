<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

trait DatabaseNotificationTrait
{
    public function shouldDatabase()
    {
        return true;
    }
}