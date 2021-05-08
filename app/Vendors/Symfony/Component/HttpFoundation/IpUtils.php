<?php

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