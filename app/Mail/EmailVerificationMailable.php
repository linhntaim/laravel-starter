<?php

namespace App\Mail;

use App\Mail\Base\NowMailable;

class EmailVerificationMailable extends NowMailable
{
    public $emailView = 'email_verification';
}