<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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