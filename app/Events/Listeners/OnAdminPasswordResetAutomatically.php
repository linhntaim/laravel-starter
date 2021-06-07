<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners;

use App\Mail\AdminPasswordResetAutomaticallyMailable;

class OnAdminPasswordResetAutomatically extends OnPasswordResetAutomatically
{
    protected function getMailable()
    {
        return new AdminPasswordResetAutomaticallyMailable();
    }
}