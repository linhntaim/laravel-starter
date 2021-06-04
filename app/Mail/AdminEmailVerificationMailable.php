<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Mail;

class AdminEmailVerificationMailable extends EmailVerificationMailable
{
    public $emailView = 'admin_email_verification';
}