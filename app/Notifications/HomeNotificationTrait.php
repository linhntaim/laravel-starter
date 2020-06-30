<?php

namespace App\Notifications;

use App\Utils\ClientApp\HomeTrait as HomeClientAppTrait;

trait HomeNotificationTrait
{
    use HomeClientAppTrait;

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
