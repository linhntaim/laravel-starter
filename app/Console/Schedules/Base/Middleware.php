<?php

namespace App\Console\Schedules\Base;

use App\Utils\SelfMiddleware\ISelfMiddleware;

abstract class Middleware implements ISelfMiddleware
{
    /**
     * @param Schedule $self
     */
    public abstract function handle($self);
}
