<?php

namespace App\Utils\ClientApp;

use App\Configuration;

trait HomeTrait
{
    use BaseTrait;

    protected function getClientAppId()
    {
        return Configuration::CLIENT_APP_HOME;
    }
}
