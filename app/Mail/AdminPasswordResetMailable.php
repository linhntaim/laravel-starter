<?php

namespace App\Mail;

class AdminPasswordResetMailable extends PasswordResetMailable
{
    public $emailView = 'admin_password_reset';
}