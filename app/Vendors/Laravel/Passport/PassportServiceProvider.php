<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Vendors\Laravel\Passport;

use App\Vendors\League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Bridge\ClientRepository;
use Laravel\Passport\Bridge\ScopeRepository;
use Laravel\Passport\PassportServiceProvider as BasePassportServiceProvider;
use League\OAuth2\Server\AuthorizationServer;

class PassportServiceProvider extends BasePassportServiceProvider
{
    public function makeAuthorizationServer()
    {
        return new AuthorizationServer(
            $this->app->make(ClientRepository::class),
            $this->app->make(AccessTokenRepository::class),
            $this->app->make(ScopeRepository::class),
            $this->makeCryptKey('private'),
            app('encrypter')->getKey(),
            new BearerTokenResponse()
        );
    }
}
