<?php

namespace App\Http\Middleware\Web;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;
use Closure;

class Settings
{
    public function handle(Request $request, Closure $next)
    {
        Facade::fetchFromRequestCookie($request);
        return $next($request);
    }
}
