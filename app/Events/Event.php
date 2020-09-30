<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events;

use App\Utils\ClientSettings\Capture;

abstract class Event
{
    use Capture;
}
