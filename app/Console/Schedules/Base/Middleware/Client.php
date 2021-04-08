<?php

namespace App\Console\Schedules\Middleware;

use App\Console\Schedules\Base\Middleware;
use App\Utils\ClientSettings\Facade;

class Client extends Middleware
{
    public function handle($self)
    {
        if ($clientId = $self->getClientId()) {
            Facade::setClient($clientId);
        }
    }
}
