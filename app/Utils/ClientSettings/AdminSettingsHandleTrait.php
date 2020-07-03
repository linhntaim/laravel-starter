<?php

namespace App\Utils\ClientSettings;

use App\Configuration;

trait AdminSettingsHandleTrait
{
    public function handle()
    {
        Facade::temporaryFromClientType(Configuration::CLIENT_APP_ADMIN, function () {
            parent::handle();
        });
    }
}
