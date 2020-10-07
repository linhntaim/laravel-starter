<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use App\Utils\CurrentScreen;
use Closure;

class Screen
{
    public function handle(Request $request, Closure $next)
    {
        $screenHeader = ConfigHelper::get('headers.screen');
        if ($request->headers->has($screenHeader) && ($screen = json_decode($request->headers->get($screenHeader), true))) {
            CurrentScreen::set($screen);
        }
        return $next($request);
    }
}
