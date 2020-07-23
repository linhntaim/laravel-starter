<?php

namespace App\Utils\ManagedFiles\Storage;

use App\Utils\ManagedFiles\Helper;

abstract class LocalStorage extends HandledStorage
{
    protected $rootDirectory;

    public function __construct($disk = null)
    {
        parent::__construct($disk);

        $this->rootDirectory = $this->config['root'];
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
}