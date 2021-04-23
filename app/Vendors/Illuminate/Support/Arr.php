<?php

namespace App\Vendors\Illuminate\Support;

use Illuminate\Support\Arr as BaseArr;

class Arr extends BaseArr
{
    public static function mergeAlongDepth($array1, $array2, $maxDepths = [], $startDepth = '')
    {
        foreach ($array2 as $key => $value) {
            $currentDepth = $startDepth ? $startDepth . '.' . $key : $key;
            if (is_array($value)
                && isset($array1[$key])
                && is_array($array1[$key])
                && (static::isAssoc($array1[$key]) || static::isAssoc($value))
                && !Str::is($maxDepths, $currentDepth)) {
                $array1[$key] = static::mergeAlongDepth($array1[$key], $value, $maxDepths, $currentDepth);
            } else {
                $array1[$key] = $value;
            }
        }
        return $array1;
    }

    /**
     * @param array|mixed $array
     * @param array $keys
     * @return array|mixed|null
     */
    public static function jsonGuard($array, $keys = [])
    {
        if (count($keys) > 0) {
            foreach ($keys as $key) {
                $array[$key] = static::jsonGuard($array[$key]);
            }
            return $array;
        }
        return is_array($array) && count($array) <= 0 ? null : $array;
    }
}
