<?php

namespace App\Mail;

use App\Mail\Base\TemplateNowMailable;

class PasswordResetMailable extends TemplateNowMailable
{
    public $emailView = 'password_reset';
}