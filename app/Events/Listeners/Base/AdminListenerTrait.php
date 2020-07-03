<?php

namespace App\Events\Listeners\Base;

use App\Configuration;
use App\Utils\ClientSettings\Facade;

trait AdminListenerTrait
{
    public function handle($event)
    {
        Facade::temporaryFromClientType(Configuration::CLIENT_APP_ADMIN, function () use ($event) {
            parent::handle($event);
        });
    }
}
