<?php

namespace App\Listeners;

use App\Utils\ClientApp\AdminTrait as AdminClientAppTrait;

trait AdminListenerTrait
{
    use AdminClientAppTrait;

    public function __construct()
    {
        $this->createClientApp();
    }

    public function __destruct()
    {
        $this->destroyClientApp();
    }
}
