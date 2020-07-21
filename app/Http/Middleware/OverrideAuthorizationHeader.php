<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use Closure;

class OverrideAuthorizationHeader
{
    public function handle(Request $request, Closure $next)
    {
        $tokenAuthorizationHeader = ConfigHelper::get('headers.token_authorization');
        if (!empty($tokenAuthorizationHeader) && $request->headers->has($tokenAuthorizationHeader)) {
            $request->headers->set('Authorization', $request->headers->get($tokenAuthorizationHeader));
        }
        return $next($request);
    }
}
