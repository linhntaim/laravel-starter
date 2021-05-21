<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners;

use App\Events\MailTestingEvent;
use App\Events\Listeners\Base\NowListener;
use App\Mail\Base\MailTrait;
use App\Mail\TestMailable;

class OnMailTestingEvent extends NowListener
{
    use MailTrait;

    /**
     * @param MailTestingEvent $event
     */
    public function go($event)
    {
        $this->mail(
            new TestMailable($event->getSubject(), $event->getView())
        );
    }
}
