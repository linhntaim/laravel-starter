<?php

namespace App\Utils\HandledFiles\Storage;

use App\Exceptions\AppException;
use App\Utils\HandledFiles\File;
use App\Utils\HandledFiles\Helper;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage as StorageFacade;

abstract class HandledStorage extends Storage implements IFileStorage, IResponseStorage
{
    /**
     * @var FilesystemAdapter
     */
    protected $disk;

    protected $config;

    protected $relativePath;

    /**
     * HandledStorage constructor.
     * @param FilesystemAdapter|null $disk
     * @throws AppException
     */
    public function __construct($disk = null)
    {
        if (!empty(static::NAME)) {
            $this->config = config(sprintf('filesystems.disks.%s', static::NAME));
        }

        $this->setDisk($disk);
    }

    /**
     * @param FilesystemAdapter|null $disk
     * @return HandledStorage
     * @throws AppException
     */
    public function setDisk($disk = null)
    {
        if (empty($disk)) {
            $disk = StorageFacade::disk(static::NAME);
        }
        if (is_string($disk)) {
            $disk = StorageFacade::disk($disk);
        }
        if (!($disk instanceof Filesystem)) {
            throw new AppException('Disk was not allowed');
        }
        $this->disk = $disk;

        return $this;
    }

    /**
     * @param string $relativePath
     * @return HandledStorage
     */
    public function setRelativePath($relativePath)
    {
        $this->relativePath = $relativePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * @return string
     */
    public function getRelativeDirectory()
    {
        return dirname($this->relativePath);
    }

    public function getFilename()
    {
        return pathinfo($this->relativePath, PATHINFO_FILENAME);
    }

    public function getBasename()
    {
        return basename($this->relativePath);
    }

    public function getExtension()
    {
        return pathinfo($this->relativePath, PATHINFO_EXTENSION);
    }

    /**
     * @param UploadedFile|File|string $file
     * @param string $toDirectory
     * @param bool|string|array $keepOriginalName
     * @return $this
     */
    public function from($file, $toDirectory = '', $keepOriginalName = true)
    {
        if ($keepOriginalName === true) {
            if ($file instanceof UploadedFile) {
                $originalName = $file->getClientOriginalName();
            } elseif ($file instanceof File) {
                $originalName = $file->getBasename();
            } else {
                $originalName = basename($file);
            }
            $this->relativePath = Helper::changeToPath($this->disk->putFileAs(Helper::noWrappedSlashes($toDirectory), $file, $originalName, 'public'));
            return $this;
        }

        $this->relativePath = Helper::changeToPath($this->disk->putFile(Helper::noWrappedSlashes($toDirectory), $file, 'public'));
        if ($keepOriginalName !== false) {
            $this->changeFilename($keepOriginalName);
        }
        return $this;
    }

    /**
     * @param $data
     * @return IUrlStorage|Storage|HandledStorage
     */
    public function setData($data)
    {
        $this->relativePath = $data;
        return $this;
    }

    public function getData()
    {
        return $this->relativePath;
    }

    public function getSize()
    {
        return $this->disk->getSize($this->relativePath);
    }

    public function getMime()
    {
        return $this->disk->getMimetype($this->relativePath);
    }

    public function getContent()
    {
        return $this->disk->get($this->relativePath);
    }

    public function getUrl()
    {
        return Helper::changeToUrl(urldecode($this->disk->url($this->relativePath)));
    }

    public function write($contents)
    {
        $this->disk->put($this->relativePath, $contents);
    }

    public function append($contents, $separator = PHP_EOL)
    {
        $this->disk->append($this->relativePath, $contents, $separator);
    }

    public function prepend($contents, $separator = PHP_EOL)
    {
        $this->disk->prepend($this->relativePath, $contents, $separator);
    }

    /**
     * @param string $toDirectory
     * @param bool|string|array $keepOriginalName
     * @param bool $override
     * @param callable $overrideCallback
     * @return HandledStorage
     * @throws
     */
    public function move($toDirectory = '', $keepOriginalName = true, $override = true, callable $overrideCallback = null)
    {
        return $this->fromTo(function ($storage, $from, $to) {
            $this->disk->move($from, $to);
        }, $toDirectory, $keepOriginalName, $override, $overrideCallback);
    }

    /**
     * @param string $toDirectory
     * @param bool|string|array $keepOriginalName
     * @param bool $override
     * @param callable $overrideCallback
     * @return HandledStorage
     * @throws
     */
    public function copy($toDirectory = '', $keepOriginalName = true, $override = true, callable $overrideCallback = null)
    {
        return $this->fromTo(function ($storage, $from, $to) {
            $this->disk->copy($from, $to);
        }, $toDirectory, $keepOriginalName, $override, $overrideCallback);
    }

    /**
     * @param callable $callback
     * @param string $toDirectory
     * @param bool|string|array $keepOriginalName
     * @param bool $override
     * @param callable $overrideCallback
     * @return HandledStorage
     * @throws
     */
    public function fromTo(callable $callback, $toDirectory = '', $keepOriginalName = true, $override = true, callable $overrideCallback = null)
    {
        if (!is_null($toDirectory) || $keepOriginalName !== true) {
            $toDirectory = is_null($toDirectory) ? $this->getRelativeDirectory() : Helper::noWrappedSlashes($toDirectory);
            if ($keepOriginalName === true) {
                $toFilename = $this->getBasename();
            } else {
                $toFilename = is_array($keepOriginalName) ?
                    Helper::nameWithExtension(
                        isset($keepOriginalName['name']) ? $keepOriginalName['name'] : null,
                        isset($keepOriginalName['extension']) ? $keepOriginalName['extension'] : $this->getExtension()
                    )
                    : Helper::nameWithExtension(
                        is_string($keepOriginalName) ? $keepOriginalName : null,
                        $this->getExtension()
                    );
            }
            $relativePath = Helper::concatPath($toDirectory, $toFilename);
            if ($this->exists($relativePath)) {
                if ($override) {
                    (new static())->setRelativePath($relativePath)->delete();
                } else {
                    if ($overrideCallback) $overrideCallback();
                    throw new AppException('Overriding file was not allowed');
                }
            }
            $callback($this, $this->relativePath, $relativePath);
            $this->relativePath = $relativePath;
        }
        return $this;
    }

    /**
     * @param string|array $filename
     * @return $this
     */
    public function changeFilename($filename)
    {
        return $this->move(null, $filename);
    }

    public function delete()
    {
        $this->disk->delete($this->relativePath);
        return $this;
    }

    public function deleteRelativeDirectory($relativeDirectory = null)
    {
        $this->disk->deleteDirectory($relativeDirectory ? $relativeDirectory : $this->getRelativeDirectory());
        return $this;
    }

    public function exists($relativePath)
    {
        return $this->disk->exists($relativePath);
    }

    public function makeDirectory($relativeDirectory)
    {
        $this->disk->makeDirectory($relativeDirectory);
        return $this;
    }

    public function first(callable $conditionCallback, $inRelativeDirectory = '', $all = false)
    {
        return $this->find($conditionCallback, $inRelativeDirectory, $all)->first();
    }

    public function find(callable $conditionCallback, $inRelativeDirectory = '', $all = false)
    {
        return collect($this->disk->files($inRelativeDirectory, $all))->filter($conditionCallback);
    }

    public function responseFile($mime, $headers = [])
    {
        return $this->disk->response($this->relativePath, null, $headers);
    }

    public function responseDownload($name, $mime, $headers = [])
    {
        return $this->disk->download($this->relativePath, $name, $headers);
    }
}
