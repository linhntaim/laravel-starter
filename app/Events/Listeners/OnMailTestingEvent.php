<?php

namespace App\Events\Listeners;

use App\Events\MailTestingEvent;
use App\Events\Listeners\Base\NowListener;
use App\Utils\Mail\MailHelper;

class OnMailTestingEvent extends NowListener
{
    /**
     * @param MailTestingEvent $event
     */
    public function go($event)
    {
        MailHelper::sendTestMailNow();
    }
}
