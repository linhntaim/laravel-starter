<?php

namespace App\Utils\HandledFiles\Storage;

class ExternalStorage extends Storage implements IUrlStorage
{
    const NAME = 'external';

    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getData()
    {
        return $this->url;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
