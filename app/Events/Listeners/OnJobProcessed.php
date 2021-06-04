<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use App\Vendors\Illuminate\Support\Facades\App;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;

class OnJobProcessed extends NowListener
{
    /**
     * @param JobProcessed $event
     */
    protected function go($event)
    {
        if (App::runningInDebug()) {
            Log::info(
                sprintf(
                    'Job [%s] was processed.',
                    $event->job->resolveName()
                )
            );
        }
    }
}
