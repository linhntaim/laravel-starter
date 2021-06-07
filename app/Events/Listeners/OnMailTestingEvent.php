<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners;

use App\Events\TestMailEvent;
use App\Events\Listeners\Base\NowListener;
use App\Mail\TestMailable;
use Illuminate\Support\Facades\Mail;

class OnMailTestingEvent extends NowListener
{
    /**
     * @param TestMailEvent $event
     */
    public function go($event)
    {
        Mail::send(
            new TestMailable($event->getSubject(), $event->getView())
        );
    }
}
