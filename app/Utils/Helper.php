<?php

namespace App\Utils;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Helper
{
    public static function default($value, $default = null, callable $callback = null)
    {
        if (filled($value)) {
            return $callback ? $callback($value) : $value;
        }
        return $default;
    }

    public static function currentUserId($default = null)
    {
        $currentUser = request()->user();
        return empty($currentUser) ? $default : $currentUser->id;
    }

    public static function table($table, $connection = null)
    {
        if (empty($connection)) {
            $connection = config('database.default');
        }
        return DB::connection($connection)->getTablePrefix() . $table;
    }

    public static function column($column, $table, $connection = null)
    {
        if (empty($connection)) {
            $connection = config('database.default');
        }
        return sprintf('%s.%s', static::table($table, $connection), $column);
    }

    public static function runInProductionMode()
    {
        return config('app.env') == 'production';
    }

    public static function runningInWindowsOS()
    {
        return version_compare(PHP_VERSION, '7.2.0', '>=') ?
            PHP_OS_FAMILY == 'Windows' : PHP_OS == 'WINNT';
    }

    public static function isUnsignedInteger($input)
    {
        return preg_match('/^\d+$/', $input) === 1;
    }

    public static function isInteger($input)
    {
        return preg_match('/^[+-]?\d+$/', $input) === 1;
    }
}
