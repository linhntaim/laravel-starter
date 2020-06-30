<?php

namespace App\Utils\Files;

use App\Utils\ClassTrait;
use Illuminate\Support\Facades\Storage;

abstract class RelativeFileContainer
{
    use ClassTrait;

    /**
     * @return \Illuminate\Http\File
     */
    public abstract function getFile();

    public function getContent()
    {
        return file_get_contents($this->getRealPath());
    }

    public function getRealPath()
    {
        return $this->getFile()->getPathname();
    }

    public function isRelative()
    {
        return FileHelper::getInstance()->isRelativePath($this->getRealPath());
    }

    public function getRealDirectory()
    {
        return dirname($this->getRealPath());
    }

    public function getRelativePath()
    {
        return FileHelper::getInstance()->toRelativePath($this->getRealPath());
    }

    public function getRelativeDirectory()
    {
        return FileHelper::getInstance()->toRelativePath($this->getRealDirectory());
    }

    public function getUrl()
    {
        return FileHelper::getInstance()->toUrl($this->getRelativePath());
    }

    public function getBaseUrl()
    {
        return FileHelper::getInstance()->toUrl($this->getRelativeDirectory());
    }

    public function getBaseName()
    {
        return $this->getFile()->getBasename();
    }

    public function getSize()
    {
        return $this->getFile()->getSize();
    }

    public function getExtension()
    {
        return $this->getFile()->getExtension();
    }

    public function getMimeType()
    {
        return $this->getFile()->getMimeType();
    }

    public function getResponse()
    {
        return response()->file($this->getRealPath());
    }

    public function getCloudPath()
    {
        return $this->getRelativePath();
    }

    public function storeCloud()
    {
        Storage::cloud()->putFileAs($this->getRelativeDirectory(), $this->getFile(), $this->getBaseName(), 'public');
        return $this;
    }

    public function getCloudUrl()
    {
        return FileHelper::getInstance()->changeToUrl(urldecode(Storage::cloud()->url($this->getRelativePath())));
    }
}
