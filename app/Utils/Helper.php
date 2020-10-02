<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\IpUtils;

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

    public static function table($table, $connection = null, $as = null)
    {
        if (empty($connection)) {
            $connection = config('database.default');
        }
        return $as ? DB::raw(sprintf('%s%s as %s', DB::connection($connection)->getTablePrefix(), $table, $as))
            : DB::connection($connection)->getTablePrefix() . $table;
    }

    public static function column($column, $table, $connection = null, $as = null)
    {
        if (empty($connection)) {
            $connection = config('database.default');
        }
        return $as ? DB::raw(sprintf('%s.%s as %s', static::table($table, $connection), $column, $as))
            : sprintf('%s.%s', static::table($table, $connection), $column);
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

    public static function matchedIps($matchingIps, $matchedIps)
    {
        foreach ($matchingIps as $matchingIp) {
            if (IpUtils::checkIp($matchingIp, $matchedIps)) return true;
        }
        return false;
    }
}
