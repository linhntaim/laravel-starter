<?php

namespace App\Http\Middleware;

use App\Utils\Framework\ClientLimiter;
use Closure;
use App\Http\Requests\Request;

class CheckForClientLimitation
{
    /**
     * The URIs that should be accessible while client limitation mode is enabled.
     *
     * @var array
     */
    protected $except = [
        '/api/prerequisite',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (($clientLimiter = (new ClientLimiter())->retrieve()) && !$clientLimiter->canAccess($request, $this->except)) {
            abort(403, 'Client limitation');
        }

        return $next($request);
    }
}
