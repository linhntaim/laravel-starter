<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClassTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    use ClassTrait;

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }

    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            static::__transErrorWithModule('unauthenticated'), $guards, $this->redirectTo($request)
        );
    }
}
