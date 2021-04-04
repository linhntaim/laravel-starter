<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

use App\Utils\ClientSettings\Facade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StringHelper
{
    public static function fill($text, $length, $char)
    {
        $textLength = mb_strlen($text);
        return $textLength >= $length ?
            $text : str_repeat($char, $length - $textLength) . $text;
    }

    public static function fillFollow($text, $followText, $char)
    {
        return static::fill($text, mb_strlen($followText), $char);
    }

    public static function hashRandom($length = 32, &$random = null)
    {
        $random = Str::random($length);
        return static::hash($random);
    }

    public static function hash($text)
    {
        return Hash::make($text);
    }

    public static function uuid()
    {
        return Str::uuid()->toString();
    }

    public static function toUtf8($text)
    {
        if (trim($text) === '') return '';

        $utf8Encoding = 'UTF-8';
        $currentEncoding = mb_detect_encoding($text);
        return $currentEncoding === false || $currentEncoding == $utf8Encoding ?
            $text : mb_convert_encoding($text, $utf8Encoding, $currentEncoding);
    }

    /**
     * @param string|array $text
     * @param null|string $locale
     * @return string|null
     */
    public static function locale($text, $locale = null)
    {
        if (is_string($text)) return $text;

        if (is_null($locale)) $locale = Facade::getLocale();
        if (is_array($text)) {
            if (isset($text[$locale])) {
                return $text[$locale];
            }
        }
        return null;
    }
}
