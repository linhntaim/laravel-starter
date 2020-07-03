<?php

namespace App\Utils\Facades;

use App\Utils\ClientSettings\DateTimer;
use App\Utils\ClientSettings\Manager;
use App\Utils\ClientSettings\NumberFormatter;
use App\Utils\ClientSettings\Settings;
use Illuminate\Support\Facades\Facade;

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
class ClientSettings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
