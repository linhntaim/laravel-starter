<?php

namespace App\Vendors\Illuminate\Support\Facades;

use App\Utils\HandledFiles\Helper;
use Illuminate\Support\Facades\App as BaseApp;
use Illuminate\Support\Facades\Log;

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

    protected static $benchAt = [];

    public static function benchFrom($name)
    {
        if (static::runningInDebug()) {
            static::$benchAt[$name] = [
                't' => microtime(true),
                'u' => memory_get_usage(),
                'ur' => memory_get_usage(true),
                'p' => memory_get_peak_usage(),
                'pr' => memory_get_peak_usage(true),
            ];
        }
    }

    public static function bench($name)
    {
        if (static::runningInDebug()) {
            if (isset(static::$benchAt[$name])) {
                Log::info(
                    sprintf(
                        'Bench from [%s]: %sms + %sms, %s / %s, %s / %s (real), %s / %s (peak), %s / %s (peak real).',
                        $name,
                        number_format(round((microtime(true) - static::$benchAt[$name]['t']) * 1000, 2), 2),
                        number_format(round((static::$benchAt[$name]['t'] - LARAVEL_START) * 1000, 2), 2),
                        Helper::autoDisplaySize(memory_get_usage(), 2),
                        Helper::autoDisplaySize(static::$benchAt[$name]['u'], 2),
                        Helper::autoDisplaySize(memory_get_usage(true), 2),
                        Helper::autoDisplaySize(static::$benchAt[$name]['ur'], 2),
                        Helper::autoDisplaySize(memory_get_peak_usage(), 2),
                        Helper::autoDisplaySize(static::$benchAt[$name]['p'], 2),
                        Helper::autoDisplaySize(memory_get_peak_usage(true), 2),
                        Helper::autoDisplaySize(static::$benchAt[$name]['pr'], 2)
                    )
                );
            } else {

                Log::info(
                    sprintf(
                        'Bench [%s] from start: %sms, %s, %s (real), %s (peak), %s (peak real).',
                        $name,
                        number_format(round((microtime(true) - LARAVEL_START) * 1000, 2), 2),
                        Helper::autoDisplaySize(memory_get_usage(), 2),
                        Helper::autoDisplaySize(memory_get_usage(true), 2),
                        Helper::autoDisplaySize(memory_get_peak_usage(), 2),
                        Helper::autoDisplaySize(memory_get_peak_usage(true), 2)
                    )
                );
            }
        }
    }
}
