<?php

namespace App\Utils\ClientSettings;

use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * Class ClientSettings
 * @package App\Utils\Facades
 * @method static Manager autoFetch()
 * @method static Manager temporaryFromUser(mixed $user, callable $callback)
 * @method static Manager temporaryFromClientType(string $clientType, callable $callback)
 * @method static Manager temporary(array|Settings $settings, callable $callback)
 * @method static DateTimer dateTimer()
 * @method static NumberFormatter numberFormatter()
 * @method static Settings capture()
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
