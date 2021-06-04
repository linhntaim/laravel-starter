<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;
use App\Utils\ConfigHelper;
use Closure;

class CustomTimezone
{
    public function handle(Request $request, Closure $next, $timezone = null)
    {
        if (is_null($timezone)) {
            $timezone = ConfigHelper::get('custom_timezone');
        }
        if (!is_null($timezone)) {
            Facade::update([
                'timezone' => $timezone,
            ]);
        }
        return $next($request);
    }
}