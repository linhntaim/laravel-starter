<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClassTrait;
use Closure;

class AuthorizedWithAdmin
{
    use ClassTrait, AdminMiddlewareTrait;

    public function handle(Request $request, Closure $next)
    {
        $admin = $this->getAdmin($request);
        if (empty($admin)) {
            return abort(403, static::__transErrorWithModule('must_be_admin'));
        }

        return $next($request);
    }
}
