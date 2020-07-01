<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use App\Utils\CryptoJs\AES;

class AuthenticatedByPassportViaCookie
{
    use AuthenticatedByPassportTrait;

    public function handle(Request $request, Closure $next)
    {
        $defaultCookieName = ConfigHelper::get('app.cookie.names.default');
        if (!auth()->check() && $request->hasCookie($defaultCookieName)) {
            $token = json_decode(AES::decrypt($request->cookie($defaultCookieName), ConfigHelper::get('app.cookie.secret')));
            if ($token !== false && isset($token->access_token) && isset($token->token_type) && isset($token->refresh_token) && isset($token->token_end_time)) {
                $this->authenticate($request, $token->token_type . ' ' . $token->access_token);
            }
        }

        return $next($request);
    }
}
