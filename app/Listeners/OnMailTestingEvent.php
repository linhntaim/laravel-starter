<?php

namespace App\Listeners;

use App\Events\MailTestingEvent;
use App\Listeners\Base\NowListener;
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
