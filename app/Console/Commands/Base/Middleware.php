<?php

namespace App\Console\Commands\Base;

use App\Utils\SelfMiddleware\ISelfMiddleware;

abstract class Middleware implements ISelfMiddleware
{
    /**
     * @param Command $self
     */
    public abstract function handle($self);
}
