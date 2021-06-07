<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Models\Base\IUser;
use App\Models\Base\IHasEmailVerified;
use App\Utils\AbortTrait;
use Closure;

class AuthorizedWithUserEmail
{
    use AbortTrait;

    public function handle(Request $request, Closure $next)
    {
        if (!$this->hasVerified($request)) {
            $this->whenError();
        }
        return $next($request);
    }

    protected function whenError()
    {
        $this->abort403('Not authorized: Email is not verified.');
    }

    /**
     * @param Request $request
     * @return IUser|null
     */
    protected function getUser(Request $request)
    {
        return $request->user();
    }

    protected function hasVerified(Request $request)
    {
        $user = $this->getUser($request);
        if (is_null($user)) {
            return false;
        }
        if ($user instanceof IHasEmailVerified) {
            return $user->hasVerifiedEmail();
        }
        return true;
    }
}