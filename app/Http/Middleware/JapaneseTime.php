<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;
use Closure;

class JapaneseTime
{
    public function handle(Request $request, Closure $next)
    {
        Facade::update([
            'timezone' => 'Asia/Tokyo',
        ]);
        return $next($request);
    }
}