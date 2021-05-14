<?php

namespace App\Vendors\Illuminate\Support\Facades;

use App\Utils\ConfigHelper;
use App\Utils\HandledFiles\Helper;
use Illuminate\Support\Facades\App as BaseApp;
use Illuminate\Support\Facades\Log;

class App extends BaseApp
{
    public static function runningInProduction()
    {
        static $run = null;
        setIfNull($run, function () {
            return config('app.env') == 'production';
        });
        return $run;
    }

    public static function runningInMultipleInstances()
    {
        static $run = null;
        setIfNull($run, function () {
            return ConfigHelper::get('multiple_instances');
        });
        return $run;
    }

    public static function runningFromRequest()
    {
        static $run = null;
        setIfNull($run, function () {
            return !static::runningInConsole() && !static::runningUnitTests();
        });
        return $run;
    }

    public static function notRunningFromRequest()
    {
        static $run = null;
        setIfNull($run, function () {
            return static::runningInConsole() || static::runningUnitTests();
        });
        return $run;
    }

    public static function runningInWindowsOs()
    {
        static $run = null;
        setIfNull($run, function () {
            return version_compare(PHP_VERSION, '7.2.0', '>=') ?
                PHP_OS_FAMILY == 'Windows' : PHP_OS == 'WINNT';
        });
        return $run;
    }

    public static function runningInDebug()
    {
        static $run = null;
        setIfNull($run, function () {
            return config('app.debug');
        });
        return $run;
    }

    protected static $benchAt = [];

    /**
     * @param string $name
     */
    public static function benchFrom(string $name)
    {
        if (static::runningInDebug()) {
            static::$benchAt[$name] = [
                't' => microtime(true),
                'u' => memory_get_usage(),
                'ur' => memory_get_usage(true),
                'p' => memory_get_peak_usage(),
                'pr' => memory_get_peak_usage(true),
            ];
            Log::info(
                sprintf(
                    'Bench start [%s].',
                    $name
                )
            );
        }
    }

    /**
     * @param string $name
     * @param boolean|string $benchFrom
     */
    public static function bench(string $name, $benchFrom = false)
    {
        if (static::runningInDebug()) {
            if (isset(static::$benchAt[$name])) {
                Log::info(
                    sprintf(
                        'Bench end [%s]: %sms + %sms, %s / %s, %s / %s (real), %s / %s (peak), %s / %s (peak real).',
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
            }
            else {
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
            if ($benchFrom === true) {
                static::benchFrom($name);
            }
            elseif ($benchFrom !== false) {
                static::benchFrom($benchFrom);
            }
        }
    }
}
