<?php

namespace App\Utils\SelfMiddleware;

interface ISelfMiddleware
{
    public function handle($self);
}
