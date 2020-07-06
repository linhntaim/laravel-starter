<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Requests\Request;

class AuthenticatedByPassportViaRequest
{
    use AuthenticatedByPassportTrait;

    public function handle(Request $request, Closure $next)
    {
        $bearerToken = null;
        if ($request->has('_x_token_type') && $request->has('_x_access_token')) {
            $bearerToken = $request->input('_x_token_type') . ' ' . $request->input('_x_access_token');
        } elseif ($request->has('_x_authorization')) {
            $bearerToken = $request->input('_x_authorization');
        }
        if (!empty($bearerToken)) {
            $this->authenticate($request, $bearerToken);
        }

        return $next($request);
    }
}
