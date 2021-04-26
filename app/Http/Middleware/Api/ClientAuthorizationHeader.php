<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware\Api;

use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use Closure;

class ClientAuthorizationHeader
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->ifHeader(ConfigHelper::get('client.header_token_authorization'), $headerValue)
            && filled($headerValue)) {
            $request->headers->set('Authorization', $headerValue);
        }
        return $next($request);
    }
}
