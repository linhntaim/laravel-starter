<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\AbortTrait;
use Closure;

class AuthorizedWithAdminPermissions
{
    use AbortTrait, AdminMiddlewareTrait;

    public function handle(Request $request, Closure $next, $permissions = null)
    {
        $admin = $this->getAdmin($request);
        if (empty($admin)) return $this->abort403();

        if (empty($permissions)) return $next($request);

        $prePermissions = explode('|', $permissions);
        foreach ($prePermissions as $prePermission) {
            $parts = explode('!', $prePermission); // first: permission or first: branch method, second: permission
            if (count($parts) == 1) {
                if ($admin->hasPermission($parts[0])) {
                    return $next($request);
                }
            } else {
                if ($request->has($parts[0])) {
                    if ($admin->hasPermissionsAtLeast(explode('#', $parts[1]))) {
                        return $next($request);
                    } else {
                        return $this->abort403();
                    }
                }
            }
        }

        return $this->abort403();
    }
}
