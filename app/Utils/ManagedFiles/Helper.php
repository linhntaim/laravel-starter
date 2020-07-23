<?php

namespace App\Utils\ManagedFiles;

use Illuminate\Support\Str;

class Helper
{
    public static function concatPath()
    {
        $paths = [];
        foreach (func_get_args() as $arg) {
            if (is_array($arg)) {
                array_push($paths, static::concatPath(...$arg));
            } else {
                $paths[] = $arg;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $paths);
    }

    public static function noWrappedSlashes($path)
    {
        return trim($path, '\\/');
    }

    public static function randomName()
    {
        return Str::random(40);
    }
}
