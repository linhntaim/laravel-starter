<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;
use App\Utils\ConfigHelper;
use Closure;

class CustomLocale
{
    public function handle(Request $request, Closure $next, $locale = null)
    {
        if (is_null($locale)) {
            $locale = ConfigHelper::get('custom_locale');
        }
        if (!is_null($locale)) {
            Facade::update([
                'locale' => $locale,
            ]);
        }
        return $next($request);
    }
}