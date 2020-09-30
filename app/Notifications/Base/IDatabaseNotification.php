<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

use Illuminate\Notifications\DatabaseNotification;

interface IDatabaseNotification
{
    static function makeFromModel(DatabaseNotification $notification);
}