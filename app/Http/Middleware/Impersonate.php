<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\ModelRepositories\OAuthImpersonateRepository;
use Closure;
use Illuminate\Support\Facades\Auth;

class Impersonate
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if (is_null($guard)) {
                $guard = config('auth.defaults.guard');
            }
            if ($guard == 'api') {
                $config = config('auth.guards.api');
                if ($config['driver'] == 'passport') {
                    $user = $request->user();
                    $oAuthImpersonate = (new OAuthImpersonateRepository())->notStrict()
                        ->getByUserIdAndAccessTokenId($user->id, $user->token()->id);
                    if ($oAuthImpersonate) {
                        $request->setImpersonator($oAuthImpersonate->admin);
                    }
                }
            }
        }

        return $next($request);
    }
}