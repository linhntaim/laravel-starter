<?php

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use App\Utils\LogHelper;
use Illuminate\Mail\Events\MessageSent;

class OnMessageSent extends NowListener
{
    /**
     * @param MessageSent $event
     */
    protected function go($event)
    {
        if (config('app.debug')) {
            LogHelper::info(sprintf('%s was sent to %s', $event->message->getSubject(), json_encode($event->message->getTo())));
        }
    }
}
