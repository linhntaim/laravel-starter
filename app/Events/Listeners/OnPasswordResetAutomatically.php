<?php

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use App\Events\PasswordResetAutomaticallyEvent;
use App\Utils\Mail\TemplateMailable;

class OnPasswordResetAutomatically extends NowListener
{
    /**
     * @param PasswordResetAutomaticallyEvent $event
     */
    protected function go($event)
    {
        $this->sendMail($event);
    }

    /**
     * @param PasswordResetAutomaticallyEvent $event
     */
    protected function getMailParams($event)
    {
        return array_merge(parent::getMailParams($event), [
            TemplateMailable::EMAIL_TO => $event->user->preferredEmail(),
            TemplateMailable::EMAIL_TO_NAME => $event->user->preferredName(),
            'password' => $event->password,
        ]);
    }

    protected function getMailTemplate($event)
    {
        return 'password_reset_automatically';
    }
}