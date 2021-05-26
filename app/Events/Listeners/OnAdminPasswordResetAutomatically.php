<?php

namespace App\Events\Listeners;

use App\Mail\AdminPasswordResetAutomaticallyMailable;

class OnAdminPasswordResetAutomatically extends OnPasswordResetAutomatically
{
    protected function getMailable()
    {
        return new AdminPasswordResetAutomaticallyMailable();
    }
}