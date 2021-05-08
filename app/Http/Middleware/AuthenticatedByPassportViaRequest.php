<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Configuration;
use App\Http\Requests\Request;
use Closure;

class AuthenticatedByPassportViaRequest
{
    use AuthenticatedByPassportTrait;

    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            $bearerToken = null;
            if ($request->has(Configuration::REQUEST_PARAM_TOKEN_TYPE) && $request->has(Configuration::REQUEST_PARAM_ACCESS_TOKEN)) {
                $bearerToken = $request->input(Configuration::REQUEST_PARAM_TOKEN_TYPE) . ' ' . $request->input(Configuration::REQUEST_PARAM_ACCESS_TOKEN);
            }
            elseif ($request->has(Configuration::REQUEST_PARAM_AUTHORIZATION)) {
                $bearerToken = $request->input(Configuration::REQUEST_PARAM_AUTHORIZATION);
            }
            if (!empty($bearerToken)) {
                $this->authenticate($request, $bearerToken);
            }
        }
        return $next($request);
    }
}
