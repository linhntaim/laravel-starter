<?php

namespace App\Utils\Files\FileWriter;

use App\Utils\Files\FileHelper;
use App\Utils\Files\RelativeFileContainer;
use Illuminate\Http\File;

class FileWriter extends RelativeFileContainer
{
    protected $filePath;
    protected $handler;

    public function __construct($name = null, $stored = false, $toDirectory = '', $isRelative = false)
    {
        $fileHelper = FileHelper::getInstance();
        $toDirectory = $stored ? $fileHelper->storePath()
            : (empty($toDirectory) ? $fileHelper->defaultPath()
                : $fileHelper->autoDirectory($toDirectory, $isRelative));
        $fileHelper->checkDirectory($toDirectory);
        $this->filePath = $fileHelper->concatPath(
            $toDirectory,
            $fileHelper->autoFilename($name)
        );
    }

    public function getFile()
    {
        return new File($this->filePath);
    }

    public function getRealPath()
    {
        return $this->filePath;
    }

    public function openToWrite()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->filePath, 'w');
        }
        return $this;
    }

    public function openToAppend()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->filePath, 'a');
        }
        return $this;
    }

    public function write($anything)
    {
        if (is_resource($this->handler)) {
            fwrite($this->handler, $anything);
        }
        return $this;
    }

    public function close()
    {
        if (is_resource($this->handler)) {
            fclose($this->handler);
        }
        return $this;
    }

    public function __destruct()
    {
        $this->close();
    }
}
