<?php

namespace App\Listeners;

use App\Events\TestingEvent;
use App\Utils\Mail\MailHelper;

class SendEmailOfTesting extends NowListener
{
    public function handle(TestingEvent $event)
    {
        MailHelper::sendTestMail();
    }
}
