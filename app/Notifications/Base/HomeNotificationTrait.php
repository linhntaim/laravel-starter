<?php

namespace App\Notifications\Base;

use App\Configuration;
use App\Models\Base\IUser;
use App\Utils\ClientSettings\Facade;

trait HomeNotificationTrait
{
    protected function resolveData($via, IUser $notifiable, $dataCallback)
    {
        return Facade::temporaryFromClientType(
            Configuration::CLIENT_APP_HOME,
            function () use ($via, $notifiable, $dataCallback) {
                return parent::resolveData($via, $notifiable, $dataCallback);
            }
        );
    }
}
