<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Guards\TokenGuard;
use Laravel\Passport\PassportUserProvider;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;

trait AuthenticatedByPassportTrait
{
    protected function authenticate(Request $request, $bearerToken)
    {
        $request->headers->set('Authorization', $bearerToken);
        $app = app();
        $config = config(sprintf('auth.guards.%s' . ($request->is('api/*') ? 'api' : 'web')));
        $user = (new TokenGuard(
            $app->make(ResourceServer::class),
            new PassportUserProvider(Auth::createUserProvider($config['provider']), $config['provider']),
            $app->make(TokenRepository::class),
            $app->make(ClientRepository::class),
            $app->make('encrypter')
        ))->user($request);

        if (!empty($user)) {
            auth()->login($user);
        }
    }
}
