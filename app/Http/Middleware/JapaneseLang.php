<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;
use Closure;

class JapaneseLang
{
    public function handle(Request $request, Closure $next)
    {
        Facade::update([
            'locale' => 'ja',
        ]);
        return $next($request);
    }
}