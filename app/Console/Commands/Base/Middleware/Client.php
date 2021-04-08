<?php

namespace App\Console\Commands\Middleware;

use App\Console\Commands\Base\Middleware;
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
