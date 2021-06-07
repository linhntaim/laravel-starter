<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;
use App\Utils\ClientSettings\Manager;
use Closure;

abstract class Client
{
    public function handle(Request $request, Closure $next)
    {
        $this->setClient($request);
        return $next($request);
    }

    /**
     * @param Request $request
     * @return Manager
     */
    protected function setClient(Request $request)
    {
        return Facade::getFacadeRoot();
    }
}
