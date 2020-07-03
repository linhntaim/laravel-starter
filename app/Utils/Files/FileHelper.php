<?php

namespace App\Utils\Files;

use App\Exceptions\AppException;
use App\Utils\ClassTrait;
use App\Utils\ClientSettings\DateTimer;
use App\Utils\ClientSettings\NumberFormatter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileHelper
{
    use ClassTrait;

    protected static $instance;

    public static function getInstance()
    {
        if (empty(static::$instance)) {
            return new FileHelper();
        }
        return static::$instance;
    }

    const STORE_FOLDER = 'store';

    private $fileSizeType = ['byte', 'bytes', 'KB', 'MB', 'GB'];

    private $fileUrl;
    private $filePath;
    private $defaultPath;

    private function __construct()
    {
        $this->fileUrl = config('files.url');
        $this->filePath = config('files.path');
        $this->defaultPath = config('filesystems.disks.local.root');
    }

    public function defaultPath()
    {
        return $this->defaultPath;
    }

    public function storePath()
    {
        $now = DateTimer::syncNowObject();
        return $this->concatPath($this->filePath, static::STORE_FOLDER, $now->format('Y'), $now->format('m'), $now->format('d'), $now->format('H'));
    }

    public function deleteUrl($fileUrl)
    {
        $filePath = $this->concatPath($this->filePath, $this->changeToPath(str_replace($this->fileUrl, '', $fileUrl)));
        if (file_exists($filePath)) {
            return @unlink($filePath);
        }
        return false;
    }

    public function toDefaultRealPath($relativePath)
    {
        if (empty($relativePath)) return $this->defaultPath;
        return $this->concatPath($this->defaultPath, $relativePath);
    }

    public function toRealPath($relativePath)
    {
        if (empty($relativePath)) return $this->filePath;
        return $this->concatPath($this->filePath, $relativePath);
    }

    public function toRelativePath($realPath)
    {
        return trim(str_replace($this->filePath, '', $realPath), DIRECTORY_SEPARATOR);
    }

    public function isRelativePath($realPath)
    {
        return Str::startsWith($realPath, $this->filePath);
    }

    public function fileExists($path, $isRelative = true)
    {
        return file_exists($isRelative ? $this->toRealPath($path) : $path);
    }

    public function toUrl($path, $isRelative = true)
    {
        return $this->concatUrl(
            $this->fileUrl,
            $this->changeToUrl($isRelative ? $path : $this->toRelativePath($path))
        );
    }

    public function autoDirectory($directory, $isRelative = true)
    {
        $directory = $isRelative ?
            $this->toRealPath($this->concatPath((array)$directory))
            : $this->concatPath((array)$directory);
        $this->checkDirectory($directory);
        return $directory;
    }

    public function autoFilename($name = null)
    {
        if (is_string($name)) {
            return $name;
        }

        $prefix = null;
        $extension = null;

        if (is_array($name)) {
            if (isset($name['prefix'])) {
                $prefix = $name['prefix'];
            }
            if (isset($name['extension'])) {
                $extension = $name['extension'];
            }
            if (isset($name['name'])) {
                $name = $name['name'];
            } else {
                $name = null;
            }
        }

        if (empty($name)) {
            return $this->randomizeFilename($prefix, $extension);
        }

        $name = $name . (empty($extension) ? '' : '.' . $extension);
        return empty($prefix) ? $name : $prefix . $name;
    }

    public function checkDirectory($directory)
    {
        if ($this->hasBackPath($directory)) {
            throw new AppException($this->__transErrorWithModule('directory_not_allowed') . ' (' . $directory . ')');
        }
        if (!is_dir($directory)) {
            if (false === @mkdir($directory, 0777, true)) {
                throw new AppException($this->__transErrorWithModule('directory_not_found') . ' (' . $directory . ')');
            }
        }
        if (!is_writable($directory)) {
            throw new AppException($this->__transErrorWithModule('directory_not_writable') . ' (' . $directory . ')');
        }

        return $directory;
    }

    public function randomizeFilename($prefix = null, $extension = null, $needTime = true, $needUnique = true, $moreUnique = true)
    {
        return sprintf('%s%s%s%s',
            $prefix,
            $needTime ? time() . '_' : '',
            $needUnique ? uniqid('', $moreUnique) : '',
            empty($extension) ? '' : '.' . $extension
        );
    }

    public function changeToUrl($path)
    {
        return str_replace('\\', '/', $path);
    }

    public function changeToPath($path)
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    public function hasBackPath($path)
    {
        return Str::startsWith('..\\', $path)
            || Str::contains('\\..\\', $path)
            || Str::startsWith('../', $path)
            || Str::contains('/../', $path);
    }

    public function concatPath()
    {
        $paths = [];
        foreach (func_get_args() as $arg) {
            if (is_array($arg)) {
                array_push($paths, $this->concatPath(...$arg));
            } else {
                $paths[] = $arg;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $paths);
    }

    public function concatUrl()
    {
        $urls = [];
        foreach (func_get_args() as $arg) {
            if (is_array($arg)) {
                array_push($urls, $this->concatUrl(...$arg));
            } else {
                $urls[] = $arg;
            }
        }
        return implode('/', $urls);
    }

    /**
     * Returns the maximum size of an uploaded file as configured in php.ini.
     *
     * @return int The maximum size of an uploaded file in bytes
     */
    public function maxUploadFileSize()
    {
        return UploadedFile::getMaxFilesize();
    }

    public function asSize($fileSize, $typeIndex = 1)
    {
        if ($fileSize > 1024) {
            return $this->asSize($fileSize / 1024, ++$typeIndex);
        }
        if ($typeIndex == 1 && $fileSize <= 1) {
            $typeIndex = 0;
        }

        return NumberFormatter::getInstance()->formatNumber($fileSize) . ' ' . $this->fileSizeType[$typeIndex];
    }

    public function delete($target)
    {
        if (is_dir($target)) {
            $files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
            foreach ($files as $file) {
                $this->delete($file);
            }
            rmdir($target);
        } elseif (is_file($target)) {
            unlink($target);
        }
    }
}
