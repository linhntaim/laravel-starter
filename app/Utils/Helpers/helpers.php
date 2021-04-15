<?php

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