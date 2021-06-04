<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use Illuminate\Queue\Events\JobProcessing;

class OnJobProcessing extends NowListener
{
    /**
     * @param JobProcessing $event
     */
    protected function go($event)
    {
    }
}
