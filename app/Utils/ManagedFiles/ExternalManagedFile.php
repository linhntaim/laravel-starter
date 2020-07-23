<?php


namespace App\Utils\ManagedFiles;


class ExternalManagedFile implements IManagedFile
{
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getFilename()
    {
        return pathinfo($this->url, PATHINFO_FILENAME);
    }
}
