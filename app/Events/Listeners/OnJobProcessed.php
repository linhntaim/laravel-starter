<?php

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;

class OnJobProcessed extends NowListener
{
    /**
     * @param JobProcessed $event
     */
    protected function go($event)
    {
        if (config('app.debug')) {
            Log::info(
                sprintf(
                    'Job [%s] was processed.',
                    $event->job->resolveName()
                )
            );
        }
    }
}
