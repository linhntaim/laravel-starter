<?php

namespace App\Utils\HandledFiles\Storage;

use App\Exceptions\AppException;
use App\Utils\HandledFiles\File;
use App\Utils\HandledFiles\Helper;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage as StorageFacade;

abstract class HandledStorage extends Storage implements IFileStorage
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

    public function getFilename()
    {
        return pathinfo($this->relativePath, PATHINFO_FILENAME);
    }

    public function getBasename()
    {
        return basename($this->relativePath);
    }

    public function from($file, $toDirectory = '', $keepOriginalName = true)
    {
        if ($keepOriginalName) {
            if ($file instanceof UploadedFile) {
                $originalName = $file->getClientOriginalName();
            } elseif ($file instanceof File) {
                $originalName = $file->getBasename();
            } else {
                $originalName = basename($file);
            }
            $this->relativePath = $this->disk->putFileAs(Helper::noWrappedSlashes($toDirectory), $file, $originalName, 'public');
        } else {
            $this->relativePath = $this->disk->putFile(Helper::noWrappedSlashes($toDirectory), $file, 'public');
        }
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
        return $this->disk->url($this->relativePath);
    }

    public function delete()
    {
        $this->disk->delete($this->relativePath);
        return $this;
    }
}