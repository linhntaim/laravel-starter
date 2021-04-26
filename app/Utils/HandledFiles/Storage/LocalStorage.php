<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Storage;

use App\Utils\HandledFiles\Helper;

/**
 * Class LocalStorage
 * @package App\Utils\HandledFiles\Storage
 * @method LocalStorage setRelativePath($relativePath)
 */
abstract class LocalStorage extends HandledStorage
{
    public const NAME = 'local';

    protected $rootDirectory;

    public function __construct($disk = null)
    {
        parent::__construct($disk);

        $this->rootDirectory = rtrim(Helper::changeToPath($this->config['root']), '/\\');
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
        $realPath = $this->getRealPath();
        Helper::makeDirectory(dirname($realPath));
        if (($resource = fopen($realPath, 'w')) !== false) {
            fclose($resource);
        }
        return $this;
    }
}
