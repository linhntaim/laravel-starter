<?php

namespace App\Utils\HandledFiles;

use App\Exceptions\AppException;
use App\Utils\ClassTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Helper
{
    use ClassTrait;

    public static function changeToUrl($path)
    {
        return str_replace('\\', '/', $path);
    }

    public static function changeToPath($path)
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    public static function concatUrl()
    {
        $urls = [];
        foreach (func_get_args() as $arg) {
            if (is_array($arg)) {
                array_push($urls, static::concatUrl(...$arg));
            } else {
                $urls[] = $arg;
            }
        }
        return implode('/', $urls);
    }

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

    public static function makeDirectory($directory)
    {
        if (!is_dir($directory)) {
            if (false === @mkdir($directory, 0777, true)) {
                throw new AppException(static::__transErrorWithModule('directory_not_found') . ' (' . $directory . ')');
            }
        }
        if (!is_writable($directory)) {
            throw new AppException(static::__transErrorWithModule('directory_not_writable') . ' (' . $directory . ')');
        }

        return $directory;
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
