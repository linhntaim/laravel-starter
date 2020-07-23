<?php

namespace App\Utils\ManagedFiles;

use Illuminate\Http\UploadedFile;
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

    public static function nameWithExtension($name = null, $extension = null)
    {
        return ($name ? $name : static::randomName()) . ($extension ? '.' . $extension : '');
    }

    public static function randomName()
    {
        return Str::random(40);
    }

    /**
     * Returns the maximum size of an uploaded file as configured in php.ini.
     *
     * @return int The maximum size of an uploaded file in bytes
     */
    public static function maxUploadFileSize()
    {
        return UploadedFile::getMaxFilesize();
    }
}
