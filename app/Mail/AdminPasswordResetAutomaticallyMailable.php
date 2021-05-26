<?php

namespace App\Mail;

class AdminPasswordResetAutomaticallyMailable extends PasswordResetAutomaticallyMailable
{
    public $emailView = 'admin_password_reset_automatically';
}