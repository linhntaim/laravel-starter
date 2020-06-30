<?php

namespace App\Utils\ClientApp;

use App\Configuration;

trait AdminTrait
{
    use BaseTrait;

    protected function getClientAppId()
    {
        return Configuration::CLIENT_APP_ADMIN;
    }
}
