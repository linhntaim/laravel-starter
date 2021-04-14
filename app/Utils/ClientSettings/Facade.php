<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ClientSettings;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * Class ClientSettings
 * @package App\Utils\Facades
 * @method static Manager setClient($clientId, $force = false)
 * @method static Manager setClientFromRequestRoute(Request $request, $force = false)
 * @method static Manager setClientFromRequestHeader(Request $request, $force = false)
 * @method static Manager decryptHeaders(Request $request)
 * @method static Manager fetchFromRequestHeader(Request $request)
 * @method static Manager fetchFromRequestCookie(Request $request)
 * @method static Manager storeCookie()
 * @method static Manager fetchFromCurrentUser()
 * @method static Manager fetchFromUser($user)
 * @method static mixed temporaryFromClient($clientId, callable $callback)
 * @method static mixed temporaryFromUser($user, callable $callback)
 * @method static mixed temporary(array|Settings $settings, callable $callback)
 * @method static Manager update(array|Settings $settings)
 * @method static DateTimer dateTimer()
 * @method static NumberFormatter numberFormatter()
 * @method static Settings capture()
 * @method static string getAppId()
 * @method static string getAppKey()
 * @method static string getAppName()
 * @method static string getAppUrl()
 * @method static string getLocale()
 * @method static string getCookie($key)
 * @method static string getPath($key)
 * @method static array getInformation()
 * @method static string getUserAgent()
 *
 * @see \App\Utils\ClientSettings\Manager
 */
class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
