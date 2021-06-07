<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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