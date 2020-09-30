<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use Closure;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (ConfigHelper::get('admin.disabled')) {
            return abort(403, 'Administration was not allowed');
        }
        return $next($request);
    }
}
