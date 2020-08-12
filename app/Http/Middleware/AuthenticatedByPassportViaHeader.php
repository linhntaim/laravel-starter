<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Requests\Request;

class AuthenticatedByPassportViaHeader
{
    use AuthenticatedByPassportTrait;

    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            if ($request->headers->has('Authorization')) {
                $this->authenticate($request, $request->headers->get('Authorization'));
            }
        }
        return $next($request);
    }
}
