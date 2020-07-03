<?php

namespace App\Utils\Traits;

use App\Configuration;
use App\Utils\Facades\ClientSettings;

trait HomeSettingsHandleTrait
{
    public function handle()
    {
        ClientSettings::temporaryFromClientType(Configuration::CLIENT_APP_HOME, function () {
            parent::handle();
        });
    }
}
