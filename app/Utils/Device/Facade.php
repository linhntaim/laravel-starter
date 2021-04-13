<?php

namespace App\Utils\Device;

use App\Http\Requests\Request;
use App\Models\Device;
use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * Class Facade
 * @package App\Utils\Device
 * @method static Manager setDevice($device)
 * @method static Device|null getDevice()
 * @method static Device|null getId()
 * @method static Manager fetchFromRequestHeader(Request $request)
 * @method static Manager fetchFromRequestCookie(Request $request)
 * @method static Manager storeCookie()
 *
 * @see \App\Utils\Device\Manager
 */
class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
