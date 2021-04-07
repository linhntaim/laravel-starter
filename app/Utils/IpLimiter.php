<?php

namespace App\Utils;

class IpLimiter
{
    use IpLimiterTrait;

    public function __construct(array $allowed = [], array $denied = [])
    {
        $this->setAllowed($allowed);
        $this->setDenied($denied);
    }
}