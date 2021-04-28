<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use Closure;

abstract class Settings
{
    public function handle(Request $request, Closure $next)
    {
        $this->fetch($request);
        return $next($request);
    }

    protected abstract function fetch(Request $request);
}
