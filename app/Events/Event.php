<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events;

use App\Utils\ClientSettings\Capture;
use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use SerializesModels, Capture;
}
