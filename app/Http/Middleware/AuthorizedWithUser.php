<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Models\Base\IUser;
use App\Utils\ClassTrait;
use Closure;

abstract class AuthorizedWithUser
{
    use ClassTrait, AdminMiddlewareTrait;

    public function handle(Request $request, Closure $next)
    {
        if (!$this->hasUser($request)) {
            if ($request->has('_login')) {
                $this->doesntHaveWhenLogin();
            }
            $this->doesntHave();
        }

        return $next($request);
    }

    /**
     * @param Request $request
     * @return IUser|null
     */
    protected abstract function getUser(Request $request);

    protected function hasUser(Request $request)
    {
        return !is_null($this->getUser($request));
    }

    protected function doesntHave()
    {
        abort(403, static::__transErrorWithModule('must_be_user'));
    }

    protected function doesntHaveWhenLogin()
    {
        abort(401, trans('passport.invalid_credentials'));
    }
}
