<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Mail;

use App\Mail\Base\TemplateNowMailable;

class PasswordResetMailable extends TemplateNowMailable
{
    public $emailView = 'password_reset';
}