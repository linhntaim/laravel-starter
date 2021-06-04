<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

use App\Http\Requests\Request;
use App\Vendors\Symfony\Component\HttpFoundation\IpUtils;

trait IpLimiterTrait
{
    /**
     * @var array
     */
    protected $allowed;

    /**
     * @var array
     */
    protected $denied;

    public function setAllowed(array $allowed = [])
    {
        $this->allowed = $allowed;
        return $this;
    }

    public function setDenied(array $denied = [])
    {
        $this->denied = $denied;
        return $this;
    }

    public function canAccess(Request $request, $excepts = [])
    {
        return $this->passRule($request, $excepts);
    }

    protected function passRule(Request $request, $except)
    {
        return $this->except($request, $except)
            || ($this->inAllowed($request) && $this->notInDenied($request));
    }

    protected function inAllowed(Request $request)
    {
        return empty($this->allowed) || IpUtils::checkIps($request->ips(), $this->allowed);
    }

    protected function notInDenied(Request $request)
    {
        return empty($this->denied) || !IpUtils::checkIps($request->ips(), $this->denied);
    }

    protected function except(Request $request, $except)
    {
        return $request->possiblyIs(...array_map(function ($except) {
            return $except === '/' ? '/' : trim($except, '/');
        }, $except));
    }
}