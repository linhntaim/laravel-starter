<?php

namespace App\Mail;

use App\Mail\Base\TemplateNowMailable;

class PasswordResetAutomaticallyMailable extends TemplateNowMailable
{
    public $emailView = 'password_reset_automatically';
}