<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClassTrait;
use App\Utils\ExtraActions\ActionFilter;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    use ClassTrait;

    protected function authenticate($request, array $guards)
    {
        $guards = ActionFilter::activate(Authenticate::class, $guards);
        parent::authenticate($request, $guards);
    }

    /**
     * @param Request $request
     * @param array $guards
     * @throws AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            static::__transErrorWithModule('unauthenticated'), $guards, $this->redirectTo($request)
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
