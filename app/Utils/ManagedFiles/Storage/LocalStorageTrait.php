<?php

namespace App\Utils\ManagedFiles\Storage;

use App\Exceptions\AppException;
use App\Utils\ManagedFiles\File;
use App\Utils\ManagedFiles\Helper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait LocalStorageTrait
{
    protected $rootDirectory;

    protected $relativePath;

    public function __construct($disk = null)
    {
        parent::__construct($disk);

        $config = config(sprintf('filesystems.disks.%s', static::NAME));
        $this->rootDirectory = $config['root'];
    }

    public function setDisk($disk)
    {
        if (empty($disk)) {
            $disk = Storage::disk(static::NAME);
        }
        parent::setDisk($disk);
    }

    public function from($file, $toDirectory = null)
    {
        if (is_string($file)) {
            return $this->fromPath($file, is_null($toDirectory) ? '' : $toDirectory);
        } elseif ($file instanceof UploadedFile) {
            return $this->fromUploaded($file, is_null($toDirectory) ? 'upload' : $toDirectory);
        }

        throw new AppException('Not supported');
    }

    public function fromUploaded(UploadedFile $uploadedFile, $toDirectory = 'upload')
    {
        $this->relativePath = $uploadedFile->store(Helper::noWrappedSlashes($toDirectory), static::NAME);
        return $this;
    }

    public function fromPath(string $filePath, $toDirectory = '')
    {
        $file = new File($filePath);
        $file->move(Helper::concatPath($this->rootDirectory, Helper::noWrappedSlashes($toDirectory)), Helper::randomName());
        $this->relativePath = str_replace($this->rootDirectory, '', $file->getRealPath());
        return $this;
    }

    public function getRealPath()
    {
        return Helper::concatPath($this->rootDirectory, $this->relativePath);
    }

    public function getFilename()
    {
        return pathinfo($this->relativePath, PATHINFO_FILENAME);
    }

    public function getData()
    {
        return $this->relativePath;
    }

    public function remove()
    {
        if (!empty($this->relativePath)) {
            @unlink($this->getRealPath());
            $this->relativePath = null;
        }
        return $this;
    }
}
