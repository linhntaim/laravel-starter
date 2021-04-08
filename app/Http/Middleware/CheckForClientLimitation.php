<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\Framework\ClientLimiter;
use Closure;

class CheckForClientLimitation
{
    /**
     * The URIs that should be accessible while client limitation mode is enabled.
     *
     * @var array
     */
    protected $except = [
        '/api/*/prerequisite',
        '/api/*/device/current',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (($clientLimiter = (new ClientLimiter())->retrieve()) && !$clientLimiter->canAccess($request, $this->except)) {
            abort(403, 'ClientApp limitation');
        }

        return $next($request);
    }
}
