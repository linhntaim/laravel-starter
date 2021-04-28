<?php

namespace App\Http\Middleware\Web;

use App\Http\Middleware\Device as BaseDevice;
use App\Http\Requests\Request;
use App\Utils\Device\Facade;

class Device extends BaseDevice
{
    protected function fetch(Request $request)
    {
        Facade::fetchFromRequestCookie($request);
    }
}
