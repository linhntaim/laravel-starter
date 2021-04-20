<?php

namespace App\Vendors\Illuminate\Support\Facades;

use Illuminate\Support\Facades\App as BaseApp;

class App extends BaseApp
{
    public static function runningInProduction()
    {
        return config('app.env') == 'production';
    }

    public static function runningFromRequest()
    {
        static $run = null;
        if (is_null($run)) {
            $run = !static::runningInConsole() && !static::runningUnitTests();
        }
        return $run;
    }

    public static function notRunningFromRequest()
    {
        static $run = null;
        if (is_null($run)) {
            $run = static::runningInConsole() || static::runningUnitTests();
        }
        return $run;
    }

    public static function runningInWindowsOs()
    {
        static $run = null;
        if (is_null($run)) {
            $run = version_compare(PHP_VERSION, '7.2.0', '>=') ?
                PHP_OS_FAMILY == 'Windows' : PHP_OS == 'WINNT';
        }
        return $run;
    }

    public static function runningInDebug()
    {
        static $run = null;
        if (is_null($run)) {
            $run = config('app.debug');
        }
        return $run;
    }
}