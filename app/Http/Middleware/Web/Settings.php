<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware\Web;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;
use App\Utils\ConfigHelper;
use App\Utils\Theme\ThemeFacade;
use Closure;

class Settings
{
    public function handle(Request $request, Closure $next)
    {
        Facade::fetchFromRequestCookie($request)->update(ConfigHelper::getClient(ThemeFacade::getAppType()));
        return $next($request);
    }
}
