<?php

namespace App\Utils\Files\FileWriter;

use App\Exceptions\AppException;
use App\Utils\ClassTrait;

abstract class ZipHandler
{
    use ClassTrait;

    protected $handler;
    protected $opened;

    public function __construct()
    {
        $this->opened = false;
    }

    public function open($zipFilePath)
    {
        if ($this->opened) {
            throw new AppException($this->__transErrorWithModule('opened'));
        }

        $this->_open($zipFilePath);
        $this->opened = true;
    }

    protected abstract function _open($zipFilePath);

    public function add($filePath, $relativeFilePath = null)
    {
        if (!$this->opened) {
            throw new AppException($this->__transErrorWithModule('not_opened'));
        }
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new AppException($this->__transErrorWithModule('not_found'));
        }

        $this->_add($filePath, $relativeFilePath);
    }

    protected abstract function _add($filePath, $relativeFilePath = null);

    public function close()
    {
        if ($this->opened) {
            $this->handler->close();
            $this->opened = false;
        }
    }

    protected abstract function _close();
}
