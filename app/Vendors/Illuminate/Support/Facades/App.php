<?php

namespace App\Vendors\Illuminate\Support\Facades;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App as BaseApp;

/**
 * Class App
 * @package App\Vendors\Illuminate\Support\Facades
 * @method static Application terminating(callable|string $callback)
 */
class App extends BaseApp
{
    public static function runningInProduction()
    {
        return config('app.env') == 'production';
    }

    public static function runningFromRequest()
    {
        return !static::runningInConsole() && !static::runningUnitTests();
    }

    public static function notRunningFromRequest()
    {
        return static::runningInConsole() || static::runningUnitTests();
    }

    public static function runningInWindowsOs()
    {
        return version_compare(PHP_VERSION, '7.2.0', '>=') ?
            PHP_OS_FAMILY == 'Windows' : PHP_OS == 'WINNT';
    }
}
