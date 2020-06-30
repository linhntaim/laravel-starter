<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use App\Utils\CryptoJs\AES;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Guards\TokenGuard;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;

class AuthenticatedByPassportViaRequest
{
    public function handle(Request $request, Closure $next)
    {
        $bearerToken = null;
        if ($request->has('_token_type') && $request->has('_access_token')) {
            $bearerToken = $request->input('_token_type') . ' ' . $request->input('_access_token');
        }
        if ($request->has('_x_authorization')) {
            $bearerToken = $request->input('_x_authorization');
        }
        if (!empty($bearerToken)) {
            $request->headers->set(
                'Authorization',
                $bearerToken
            );

            $app = app();
            $user = (new TokenGuard(
                $app->make(ResourceServer::class),
                Auth::createUserProvider('users'),
                $app->make(TokenRepository::class),
                $app->make(ClientRepository::class),
                $app->make('encrypter')
            ))->user($request);

            if (!empty($user)) {
                auth()->login($user);
            }
        }

        return $next($request);
    }
}
