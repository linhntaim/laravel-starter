<?php

function maxExecutionTime()
{
    static $maxExecutionTime = null;
    if (is_null($maxExecutionTime)) {
        $maxExecutionTime = intval(ini_get('max_execution_time'));
    }
    return $maxExecutionTime;
}

function printLine($value)
{
    print_r($value);
    echo PHP_EOL;
}

/**
 * @param string $json
 * @param bool $safe
 * @param int $depth
 * @param int $flags
 * @return array
 */
function jsonDecodeArray(string $json, bool $safe = true, int $depth = 512, int $flags = 0)
{
    $array = json_decode($json, true, $depth, $flags);

    return $safe ? (is_array($array) ? $array : []) : $array;
}

/**
 * @param string $file
 * @param bool $safe
 * @param int $depth
 * @param int $flags
 * @return array
 * @throws
 */
function fileJsonDecodeArray(string $file, bool $safe = true, int $depth = 512, int $flags = 0)
{
    if (is_file($file)) {
        return jsonDecodeArray(file_get_contents($file), $safe, $depth, $flags);
    }
    if (!$safe) {
        throw new App\Exceptions\AppException('File does not exist.');
    }
    return [];
}

function got($value, $default = null)
{
    if (filled($value)) {
        return $value;
    }

    return value($default);
}

function iif($bool, $true = null, $false = null)
{
    return is_null($true) && is_null($false) ?
        value($bool) : value(value($bool) ? (is_null($true) ? true : $true) : (is_null($false) ? false : $false));
}

function callIf($bool, callable $callback, $if = true)
{
    if ($if) {
        return value($bool) ? $callback(true) : null;
    }
    return $callback(value($bool));
}

function transIf($key, $default = null, $replace = [], $locale = null, $fallback = true)
{
    return trans()->has($key, $locale, $fallback) ? trans($key, $replace, $locale) : $default;
}

function currentUserId($default = null)
{
    return got(auth()->id(), $default);
}
