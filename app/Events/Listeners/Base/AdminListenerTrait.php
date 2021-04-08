<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners\Base;

use App\Configuration;
use App\Utils\ClientSettings\Facade;

trait AdminListenerTrait
{
    public function handle($event)
    {
        Facade::temporaryFromClient(Configuration::CLIENT_APP_ADMIN, function () use ($event) {
            parent::handle($event);
        });
    }
}
