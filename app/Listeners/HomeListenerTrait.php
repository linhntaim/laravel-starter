<?php

namespace App\Listeners;

use App\Utils\ClientApp\HomeTrait as HomeClientAppTrait;

trait HomeListenerTrait
{
    use HomeClientAppTrait;

    public function __construct()
    {
        $this->createClientApp();
    }

    public function __destruct()
    {
        $this->destroyClientApp();
    }
}
