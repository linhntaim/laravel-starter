<?php

namespace App\Utils\ClientSettings;

use App\Configuration;

trait HomeSettingsHandleTrait
{
    public function handle()
    {
        Facade::temporaryFromClientType(Configuration::CLIENT_APP_HOME, function () {
            parent::handle();
        });
    }
}
