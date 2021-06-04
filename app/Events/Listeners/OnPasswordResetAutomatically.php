<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use App\Events\PasswordResetAutomaticallyEvent;
use App\Mail\PasswordResetAutomaticallyMailable;
use Illuminate\Support\Facades\Mail;

class OnPasswordResetAutomatically extends NowListener
{
    /**
     * @param PasswordResetAutomaticallyEvent $event
     */
    protected function go($event)
    {
        Mail::send(
            $this->getMailable()
                ->to($event->user->preferredEmail(), $event->user->preferredName())
                ->with([
                    'name' => $event->user->preferredName(),
                    'password' => $event->password,
                ])
        );
    }

    /**
     * @return PasswordResetAutomaticallyMailable
     */
    protected function getMailable()
    {
        return new PasswordResetAutomaticallyMailable();
    }
}