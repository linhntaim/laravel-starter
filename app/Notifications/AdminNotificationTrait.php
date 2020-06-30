<?php

namespace App\Notifications;

use App\Utils\ClientApp\AdminTrait as AdminClientAppTrait;

trait AdminNotificationTrait
{
    use AdminClientAppTrait;

    public function __construct($fromUser = null)
    {
        $this->createClientApp();

        parent::__construct($fromUser);
    }

    public function __destruct()
    {
        $this->destroyClientApp();
    }
}
