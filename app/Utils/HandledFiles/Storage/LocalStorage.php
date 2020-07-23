<?php

namespace App\Utils\HandledFiles\Storage;

use App\Utils\HandledFiles\Helper;

/**
 * Class LocalStorage
 * @package App\Utils\ManagedFiles\Storage
 * @method LocalStorage setRelativePath($relativePath)
 */
abstract class LocalStorage extends HandledStorage
{
    protected $rootDirectory;

    public function __construct($disk = null)
    {
        parent::__construct($disk);

        $this->rootDirectory = $this->config['root'];
    }

    public function getRootPath()
    {
        return $this->rootDirectory;
    }

    public function getRealPath()
    {
        return Helper::concatPath($this->rootDirectory, $this->relativePath);
    }

    public function create($extension, $toDirectory = '')
    {
        $this->relativePath = Helper::concatPath(
            Helper::noWrappedSlashes($toDirectory),
            Helper::nameWithExtension(null, $extension)
        );
        if (($resource = fopen($this->getRealPath(), 'w')) !== false) {
            fclose($resource);
        }
        return $this;
    }

    public function move($toDirectory = '')
    {
        if (!is_null($toDirectory)) {
            $relativePath = Helper::concatPath(Helper::noWrappedSlashes($toDirectory), $this->getBasename());
            $this->disk->move($this->relativePath, $relativePath);
            $this->relativePath = $relativePath;
        }
        return $this;
    }
}