<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use Closure;

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
