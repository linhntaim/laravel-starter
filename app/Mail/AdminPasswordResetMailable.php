<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Mail;

class AdminPasswordResetMailable extends PasswordResetMailable
{
    public $emailView = 'admin_password_reset';
}