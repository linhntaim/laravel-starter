<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use Closure;

abstract class Device
{
    public function handle(Request $request, Closure $next)
    {
        $this->fetch($request);
        return $next($request);
    }

    protected abstract function fetch(Request $request);
}
