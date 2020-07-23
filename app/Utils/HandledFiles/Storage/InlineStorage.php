<?php


namespace App\Utils\HandledFiles\Storage;

class InlineStorage extends Storage
{
    const NAME = 'inline';

    protected $inline;

    public function fromFile($filePath)
    {
        if (is_file($filePath)) {
            return $this->fromContent(file_get_contents($filePath));
        }
        return $this;
    }

    public function fromContent($content)
    {
        $this->inline = base64_encode($content);
        return $this;
    }

    public function getData()
    {
        return $this->inline;
    }
}