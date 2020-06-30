<?php

namespace App\Utils\Files\FileReader;

use App\Utils\ClassTrait;
use Exception as BaseException;

abstract class FileReader
{
    use ClassTrait;

    protected $filePath;
    protected $handler;
    protected $readCounter;
    protected $useCustomException;
    protected $excludeCustomExceptions;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->readCounter = 0;
        $this->useCustomException = true;
        $this->excludeCustomExceptions = [];
    }

    public function disableCustomException($exclude = [])
    {
        if (!config('app.debug')) {
            $this->useCustomException = false;
            $this->excludeCustomExceptions = $exclude;
        }
        return $this;
    }

    protected function isExcludedException($exception)
    {
        foreach ($this->excludeCustomExceptions as $excludeCustomException) {
            if (is_a($exception, $excludeCustomException)) {
                return true;
            }
        }
        return false;
    }

    public function open()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->filePath, 'r');
        }
        return $this;
    }

    public function read(callable $callback = null)
    {
        if (is_resource($this->handler) && !feof($this->handler)) {
            ++$this->readCounter;
            return $this->trulyRead($callback);
        }
        return null;
    }

    protected abstract function trulyRead(callable $callback = null);

    protected abstract function throwReadException();

    public function readAll(callable $callback = null, callable $afterCallback = null)
    {
        if (is_resource($this->handler)) {
            $read = [];
            $this->readCounter = 0;
            while (!feof($this->handler)) {
                ++$this->readCounter;
                $read[] = $this->trulyRead($callback);
            }
            if ($afterCallback) {
                $read = $afterCallback($read);
            }
            return $read;
        }
        return null;
    }

    public function readWhole(callable $callback = null, callable $beforeCallback = null, callable $afterCallback = null)
    {
        if (is_resource($this->handler)) {
            $read = [];
            $this->readCounter = 0;
            while (!feof($this->handler)) {
                ++$this->readCounter;
                $read[] = $this->trulyRead();
            }
            if ($beforeCallback) {
                $read = $beforeCallback($read);
            }
            $this->readCounter = 0;
            foreach ($read as $key => &$data) {
                ++$this->readCounter;
                if ($callback) {
                    try {
                        $data = $callback($data, $this->readCounter);
                    } catch (BaseException $exception) {
                        if ($this->useCustomException || $this->isExcludedException($exception)) {
                            throw $exception;
                        }
                        $this->throwReadException();
                    }
                }
            }
            if ($afterCallback) {
                $read = $afterCallback($read);
            }
            return $read;
        }
        return null;
    }
}
