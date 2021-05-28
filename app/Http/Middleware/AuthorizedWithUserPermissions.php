<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Models\Base\IHasPermissions;
use App\Utils\AbortTrait;
use Closure;

abstract class AuthorizedWithUserPermissions
{
    use AbortTrait;

    public function handle(Request $request, Closure $next, $permissions = null)
    {
        if (!$this->hasPermissions($request, $permissions)) {
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
     * @return IHasPermissions|null
     */
    protected abstract function getUser(Request $request);

    /**
     * @param Request $request
     * @param string|null $permissions
     * @return bool
     */
    protected function hasPermissions(Request $request, $permissions = null)
    {
        $user = $this->getUser($request);
        if (is_null($user)) {
            return false;
        }
        if (is_null($permissions)) {
            return true;
        }
        $prePermissions = explode('|', $permissions);
        foreach ($prePermissions as $prePermission) {
            $parts = explode('!', $prePermission); // first: permission or first: branch method, second: permission
            if (count($parts) == 1) {
                if ($user->hasPermission($parts[0])) {
                    return true;
                }
            }
            else {
                if ($request->has($parts[0])) {
                    if ($user->hasPermissions(explode('#', $parts[1]))) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
