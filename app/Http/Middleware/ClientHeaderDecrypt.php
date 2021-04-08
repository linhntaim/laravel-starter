<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;
use Closure;

class ClientHeaderDecrypt
{
    public function handle(Request $request, Closure $next)
    {
        Facade::decryptHeaders($request);
        return $next($request);
    }
}
