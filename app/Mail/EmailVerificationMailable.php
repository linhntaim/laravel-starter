<?php

namespace App\Mail;

use App\Mail\Base\TemplateNowMailable;

class EmailVerificationMailable extends TemplateNowMailable
{
    public $emailView = 'email_verification';
}