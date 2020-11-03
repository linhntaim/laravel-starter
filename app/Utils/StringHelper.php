<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

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

    public static function hashRandom($length = 32)
    {
        return static::hash(Str::random($length));
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
        if (empty($text)) return '';

        $utf8Encoding = 'UTF-8';
        $currentEncoding = mb_detect_encoding($text);
        return $currentEncoding === false || $currentEncoding == $utf8Encoding ?
            $text : mb_convert_encoding($text, $utf8Encoding, $currentEncoding);
    }
}
