<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Models\Base\IUser;
use App\Models\Base\IHasEmailVerified;
use App\Utils\AbortTrait;
use Closure;

abstract class AuthorizedWithUserEmail
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
        $this->abort403();
    }

    /**
     * @param Request $request
     * @return IUser|null
     */
    protected abstract function getUser(Request $request);

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