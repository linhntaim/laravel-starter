<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware\Web;

use App\Http\Middleware\Settings as BaseSettings;
use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;

class Settings extends BaseSettings
{
    protected function fetch(Request $request)
    {
        Facade::fetchFromRequestCookie($request);
    }
}
