<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Storage;

use App\Utils\HandledFiles\File;
use Illuminate\Http\UploadedFile;

class InlineStorage extends Storage implements IFileStorage, IUrlStorage, IResponseStorage
{
    public const NAME = 'inline';

    protected $size;
    protected $mime;

    protected $inline;

    public function fromFile($file)
    {
        if ($file instanceof UploadedFile) {
            $this->size = $file->getSize();
            $this->mime = $file->getClientMimeType();
            $content = file_get_contents($file->getRealPath());
        } else {
            if (!($file instanceof File)) {
                $file = new File($file);
            }
            $this->size = $file->getSize();
            $this->mime = $file->getMimeType();
            $content = $file->getContent();
        }

        return $this->setContent($content);
    }

    /**
     * @param $data
     * @return IUrlStorage|Storage|InlineStorage
     */
    public function setData($data)
    {
        $this->inline = $data;
        return $this;
    }

    public function getData()
    {
        return $this->inline;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getMime()
    {
        return $this->mime;
    }

    public function setContent($content)
    {
        $this->inline = base64_encode($content);
        return $this;
    }

    public function getContent()
    {
        return base64_decode($this->inline);
    }

    public function getUrl()
    {
        return sprintf('data:%s;base64,%s', $this->getMime(), $this->inline);
    }

    public function responseFile($mime, $headers = [])
    {
        return response()->streamDownload(function () {
            echo $this->getContent();
        }, null, array_merge([
            'Content-Type' => $mime,
        ], $headers), 'inline');
    }

    public function responseDownload($name, $mime, $headers = [])
    {
        return response()->streamDownload(function () {
            echo $this->getContent();
        }, $name, array_merge([
            'Content-Type' => $mime,
        ], $headers));
    }
}
