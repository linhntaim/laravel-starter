<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\Utils\ClientApp\Helper as ClientAppHelper;
use App\Utils\LocalizationHelper;
use Closure;

class ApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        ClientAppHelper::fetchInstanceByRequest();
        LocalizationHelper::getInstance()->autoFetch();
        return $next($request);
    }
}
