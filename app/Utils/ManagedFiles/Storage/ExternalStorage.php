<?php

namespace App\Utils\ManagedFiles\Storage;

class ExternalStorage extends Storage
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
}
