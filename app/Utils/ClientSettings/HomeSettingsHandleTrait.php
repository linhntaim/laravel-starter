<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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
