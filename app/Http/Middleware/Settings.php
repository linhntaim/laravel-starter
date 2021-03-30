<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;
use Closure;

class Settings
{
    public function handle(Request $request, Closure $next)
    {
        Facade::fetchFromRequestHeaders($request);
        return $next($request);
    }
}
