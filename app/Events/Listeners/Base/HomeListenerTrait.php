<?php

namespace App\Events\Listeners\Base;

use App\Configuration;
use App\Utils\Facades\ClientSettings;

trait HomeListenerTrait
{
    public function handle($event)
    {
        ClientSettings::temporaryFromClientType(Configuration::CLIENT_APP_HOME, function () use ($event) {
            parent::handle($event);
        });
    }
}
