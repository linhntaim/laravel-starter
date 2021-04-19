<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class OnMessageSent extends NowListener
{
    /**
     * @param MessageSent $event
     */
    protected function go($event)
    {
        if (config('app.debug')) {
            Log::info(
                sprintf(
                    '[%s] was sent to [%s].',
                    $event->message->getSubject(),
                    json_encode($event->message->getTo())
                )
            );
        }
    }
}
