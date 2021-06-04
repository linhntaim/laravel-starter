<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Vendors\Symfony\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\IpUtils as BaseUtils;

class IpUtils extends BaseUtils
{
    public static function checkIps($requestIps, $ips)
    {
        foreach ($requestIps as $requestIp) {
            if (static::checkIp($requestIp, $ips)) {
                return true;
            }
        }
        return false;
    }
}