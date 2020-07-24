<?php

namespace App\Utils\HandledFiles\Storage;

class ExternalStorage extends Storage implements IUrlStorage, IResponseStorage
{
    const NAME = 'external';

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

    public function getUrl()
    {
        return $this->url;
    }

    public function responseFile($mime, $headers = [])
    {
        return response()->streamDownload(function () {
            echo file_get_contents($this->url);
        }, null, $mime ? array_merge([
            'Content-Type' => $mime,
        ], $headers) : $headers, 'inline');
    }

    public function responseDownload($name, $mime, $headers = [])
    {
        return response()->streamDownload(function () {
            echo file_get_contents($this->url);
        }, $name, $mime ? array_merge([
            'Content-Type' => $mime,
        ], $headers) : $headers);
    }
}
