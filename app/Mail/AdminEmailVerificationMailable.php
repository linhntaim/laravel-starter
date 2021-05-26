<?php

namespace App\Mail;

class AdminEmailVerificationMailable extends EmailVerificationMailable
{
    public $emailView = 'admin_email_verification';
}