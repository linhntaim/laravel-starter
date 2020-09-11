<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use App\Utils\CryptoJs\AES;

class AuthenticatedByPassportViaCookie
{
    use AuthenticatedByPassportTrait;

    public function handle(Request $request, Closure $next, $client = 'admin')
    {
        if (!auth()->check()) {
            $clientConfig = ConfigHelper::getClient($client);
            if (!empty($clientConfig) && isset($clientConfig['cookie'])) {
                $clientCookieConfig = $clientConfig['cookie'];
                $defaultCookieName = $clientCookieConfig['names']['default'];
                if (!auth()->check() && $request->hasCookie($defaultCookieName)) {
                    $token = json_decode(
                        AES::decrypt($request->cookie($defaultCookieName), $clientCookieConfig['secret'])
                    );
                    if (!is_null($token)
                        && isset($token->access_token)
                        && isset($token->token_type)
                        && isset($token->refresh_token)
                        && isset($token->token_end_time)) {
                        $this->authenticate($request, $token->token_type . ' ' . $token->access_token);
                    }
                }
            }
        }
        return $next($request);
    }
}
