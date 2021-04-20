<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Storage;

use App\Exceptions\AppException;

class ExternalStorage extends Storage implements IUrlStorage, IResponseStorage
{
    public const NAME = 'external';

    protected $url;

    public function fromUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param $data
     * @return IUrlStorage|Storage
     */
    public function setData($data)
    {
        $this->url = $data;
        return $this;
    }

    public function getData()
    {
        return $this->url;
    }

    public function setContent($content)
    {
        throw new AppException('Cannot set content');
    }

    public function getContent()
    {
        return file_get_contents($this->url);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function responseFile($mime, $headers = [])
    {
        return response()->streamDownload(function () {
            echo $this->getContent();
        }, null, $mime ? array_merge([
            'Content-Type' => $mime,
        ], $headers) : $headers, 'inline');
    }

    public function responseDownload($name, $mime, $headers = [])
    {
        return response()->streamDownload(function () {
            echo $this->getContent();
        }, $name, $mime ? array_merge([
            'Content-Type' => $mime,
        ], $headers) : $headers);
    }
}
