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
 * @method static Manager autoFetch()
 * @method static Manager fetchFromRequestHeaders(Request $request)
 * @method static Manager fetchFromRequestCookie(Request $request)
 * @method static Manager storeCookie()
 * @method static Manager fetchFromCurrentUser()
 * @method static Manager fetchFromUser($user)
 * @method static Manager temporaryFromUser(mixed $user, callable $callback)
 * @method static Manager temporaryFromClientType(string $clientType, callable $callback)
 * @method static Manager temporary(array|Settings $settings, callable $callback)
 * @method static Manager update($settings)
 * @method static DateTimer dateTimer()
 * @method static NumberFormatter numberFormatter()
 * @method static Settings capture()
 * @method static string getAppId()
 * @method static string getAppName()
 * @method static string getAppUrl()
 * @method static string getLocale()
 */
class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
