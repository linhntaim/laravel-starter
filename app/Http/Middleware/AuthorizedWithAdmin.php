<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClassTrait;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class AuthorizedWithAdmin
{
    use ClassTrait, AdminMiddlewareTrait;

    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        $admin = $this->getAdmin($request);
        if (empty($admin)) {
            return abort(403, static::__transErrorWithModule('must_be_admin'));
        }

        return $next($request);
    }
}
