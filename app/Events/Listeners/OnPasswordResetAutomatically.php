<?php

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use App\Events\PasswordResetAutomaticallyEvent;
use App\Mail\Base\MailTrait;
use App\Mail\PasswordResetAutomaticallyMailable;

class OnPasswordResetAutomatically extends NowListener
{
    use MailTrait;

    /**
     * @param PasswordResetAutomaticallyEvent $event
     */
    protected function go($event)
    {
        $this->mail(
            (new PasswordResetAutomaticallyMailable())
                ->to($event->user->preferredEmail(), $event->user->preferredName())
                ->with([
                    'password' => $event->password,
                ])
        );
    }
}