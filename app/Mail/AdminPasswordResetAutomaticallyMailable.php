<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Mail;

class AdminPasswordResetAutomaticallyMailable extends PasswordResetAutomaticallyMailable
{
    public $emailView = 'admin_password_reset_automatically';
}