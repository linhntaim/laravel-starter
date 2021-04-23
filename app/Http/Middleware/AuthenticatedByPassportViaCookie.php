<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;
use App\Utils\CryptoJs\AES;
use Closure;

class AuthenticatedByPassportViaCookie
{
    use AuthenticatedByPassportTrait;

    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            if ($request->ifCookie(Facade::getCookie('default'), $cookieValue)) {
                $token = json_decode(
                    AES::decrypt($cookieValue, Facade::getAppKey())
                );
                if (isset($token->tokenType) && isset($token->accessToken)) {
                    $this->authenticate($request, $token->tokenType . ' ' . $token->accessToken);
                }
            }
        }
        return $next($request);
    }
}
