<?php

namespace App\Events\Listeners\Base;

use App\Configuration;
use App\Utils\ClientSettings\Facade;

trait HomeListenerTrait
{
    public function handle($event)
    {
        Facade::temporaryFromClientType(Configuration::CLIENT_APP_HOME, function () use ($event) {
            parent::handle($event);
        });
    }
}
