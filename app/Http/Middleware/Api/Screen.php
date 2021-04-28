<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware\Api;

use App\Http\Requests\Request;
use App\Utils\Screen\Facade;
use Closure;

class Screen
{
    public function handle(Request $request, Closure $next)
    {
        $this->fetch($request);
        return $next($request);
    }

    protected function fetch(Request $request)
    {
        Facade::fetchFromRequestHeader($request);
    }
}
