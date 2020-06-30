<?php

namespace App\Console\Commands;

use App\Configuration;
use App\Utils\ClientApp\HomeTrait as HomeClientAppTrait;
use App\Utils\LocalizationHelper;

abstract class HomeCommand extends Command
{
    use HomeClientAppTrait;

    public function __construct()
    {
        parent::__construct();

        $this->createClientApp();

        LocalizationHelper::getInstance()
            ->fetchFromConfiguration(Configuration::CLIENT_APP_HOME)
            ->apply();
    }

    public function __destruct()
    {
        $this->destroyClientApp();
    }
}
