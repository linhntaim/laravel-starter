<?php

namespace App\Notifications\Base;

use App\Configuration;
use App\ModelTraits\IUser;
use App\Utils\Facades\ClientSettings;

trait AdminNotificationTrait
{
    protected function resolveData($via, IUser $notifiable, $dataCallback)
    {
        return ClientSettings::temporaryFromClientType(
            Configuration::CLIENT_APP_ADMIN,
            function () use ($via, $notifiable, $dataCallback) {
                return parent::resolveData($via, $notifiable, $dataCallback);
            }
        );
    }
}
