<?php

namespace App\Events\Listeners\Base;

use App\Configuration;
use App\Utils\Facades\ClientSettings;

trait AdminListenerTrait
{
    public function handle($event)
    {
        ClientSettings::temporaryFromClientType(Configuration::CLIENT_APP_ADMIN, function () use ($event) {
            parent::handle($event);
        });
    }
}
