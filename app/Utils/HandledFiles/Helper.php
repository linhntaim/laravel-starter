<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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

    public static function hasBackPath($path)
    {
        return Str::startsWith('..\\', $path)
            || Str::contains('\\..\\', $path)
            || Str::endsWith('\\..', $path)
            || Str::startsWith('../', $path)
            || Str::contains('/../', $path)
            || Str::endsWith('/..', $path);
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

    public static function copy($source, $destination, $context = null)
    {
        if (is_file($source)) {
            return copy($source, $destination, $context);
        }

        $dir = opendir($source);
        @mkdir($destination);
        while (false !== ($file = readdir($dir))) {
            if ($file != '.' && $file != '..') {
                if (false === static::copy($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file, $context)) {
                    return false;
                }
            }
        }
        closedir($dir);
        return true;
    }

    public static function autoDisplaySize($size, $callback = null, $unitSeparator = ' ')
    {
        $units = ['byte', 'bytes', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;

        if ($size > 1) {
            $unitIndex = 1;
            while ($size > 1024) {
                $size /= 1024;
                ++$unitIndex;
            }
        }

        if ($callback === true) {
            $size = number_format($size);
        } elseif (is_int($callback)) {
            $size = number_format($size, $callback);
        } elseif (is_callable($callback)) {
            $size = $callback($size);
        }
        return $size . $unitSeparator . $units[$unitIndex];
    }
}
