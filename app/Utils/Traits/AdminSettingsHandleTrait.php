<?php

namespace App\Utils\Traits;

use App\Configuration;
use App\Utils\Facades\ClientSettings;

trait AdminSettingsHandleTrait
{
    public function handle()
    {
        ClientSettings::temporaryFromClientType(Configuration::CLIENT_APP_ADMIN, function () {
            parent::handle();
        });
    }
}
