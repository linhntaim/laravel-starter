<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware\Api;

use App\Http\Middleware\Device as BaseDevice;
use App\Http\Requests\Request;
use App\Utils\Device\Facade;

class Device extends BaseDevice
{
    protected function fetch(Request $request)
    {
        Facade::fetchFromRequestHeader($request);
    }
}
