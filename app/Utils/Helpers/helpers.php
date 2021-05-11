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
 * @param string $pattern
 * @param string $subject
 * @param string[][]|array|null $matches
 * @param int $flags
 * @param int $offset
 * @return bool
 */
function whenPregMatchAll(string $pattern, string $subject, array &$matches = null, int $flags = PREG_PATTERN_ORDER, int $offset = 0)
{
    $matched = preg_match_all($pattern, $subject, $matches, $flags, $offset);
    return is_int($matched) && $matched > 0;
}

/**
 * @param object|string $object
 * @param string[]|array|string $interfaces
 */
function classImplemented($object, $interfaces)
{
    $implements = class_implements($object);
    foreach ((array)$interfaces as $interface) {
        if (isset($implements[$interface])) {
            return true;
        }
    }
    return false;
}

/**
 * @param object|string $object
 * @param string[]|array|string $classes
 */
function classExtended($object, $classes)
{
    $parents = class_parents($object);
    foreach ((array)$classes as $cl) {
        if (isset($parents[$cl])) {
            return true;
        }
    }
    return false;
}

/**
 * @param string|null $json
 * @param bool $safe
 * @param int $depth
 * @param int $flags
 * @return array
 */
function jsonDecodeArray($json = null, bool $safe = true, int $depth = 512, int $flags = 0)
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

function has($value, $filled = true)
{
    return ($filled && filled($value)) || (!$filled && !is_null($value));
}

function got($value, $default = null, $filled = true)
{
    if (has($value, $filled)) {
        return $value;
    }

    return value($default);
}

function iif($bool, $true = null, $false = null)
{
    return is_null($true) && is_null($false) ?
        value($bool) : value(value($bool) ? (is_null($true) ? true : $true) : (is_null($false) ? false : $false));
}

/**
 * @param mixed|null $value
 * @param callable|mixed $targetValue
 */
function setIfNull(&$value, $targetValue)
{
    if (is_null($value)) {
        $value = value($targetValue);
    }
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
