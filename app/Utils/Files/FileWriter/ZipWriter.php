<?php

namespace App\Utils\Files\FileWriter;

use App\Exceptions\AppException;

class ZipWriter extends FileWriter
{
    /**
     * @var ZipHandler
     */
    protected $handler;

    public function __construct($name, $handler = ZipArchiveHandler::class, $stored = false, $toDirectory = '', $isRelative = false)
    {
        if (is_array($name)) {
            $name['extension'] = 'zip';
        } else {
            $name = [
                'name' => $name,
                'extension' => 'zip',
            ];
        }

        parent::__construct($name, $stored, $toDirectory, $isRelative);

        $this->handler = new $handler;
    }

    public function openToWrite()
    {
        $this->handler->open($this->filePath);
        return $this;
    }

    public function openToAppend()
    {
        throw new AppException('Currently appending to zip file is not supported');
    }

    public function write($anything)
    {
        throw new AppException('Cannot write directly to zip file');
    }

    public function add($filePath, $relativeFilePath = null)
    {
        $this->handler->add($filePath, $relativeFilePath);
    }

    public function close()
    {
        $this->handler->close();
    }
}
