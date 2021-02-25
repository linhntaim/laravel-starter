<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Filer;

use App\Exceptions\AppException;
use App\Exceptions\Exception;

trait ReadFilerTrait
{
    protected $fNotRead = true;
    protected $fReadCounter = 0;
    protected $fReadLength = 0;
    protected $fReadUseCustomException = true;
    protected $fReadExcludedCustomExceptions = [];

    public function fReadSetLength($length = 0)
    {
        $this->fReadLength = $length;
        return $this;
    }

    public function fReadDisableCustomException($excluded = [])
    {
        if (!config('app.debug')) {
            $this->fReadUseCustomException = false;
            $this->fReadExcludedCustomExceptions = $excluded;
        }
        return $this;
    }

    public function fIsExcludedException($exception)
    {
        if ($this->fReadUseCustomException) return true;

        foreach ($this->fReadExcludedCustomExceptions as $excludedCustomException) {
            if (is_a($exception, $excludedCustomException)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return ReadFilerTrait|Filer
     */
    public function fStartReading()
    {
        if (is_resource($this->fResource)) {
            $this->fEndReading();
        }

        return $this->fOpen(Filer::MODE_READ);
    }

    /**
     * @return ReadFilerTrait|Filer
     */
    public function fEndReading()
    {
        $this->fClose();
        $this->fNotRead = true;
        $this->fReadCounter = 0;
        return $this;
    }

    /**
     * @return bool
     */
    public function fEndReadingIfEof()
    {
        if (feof($this->fResource)) {
            $this->fEndReading();
            return true;
        }
        return false;
    }

    public function fRead()
    {
        if (is_resource($this->fResource)) {
            if ($this->fEndReadingIfEof()) {
                return false;
            }
            return $this->fBeforeReading()
                ->fAfterReading($this->fReading());
        }
        return false;
    }

    protected function fReading()
    {
        return fread($this->fResource, $this->fReadLength ? $this->fReadLength : $this->getSize());
    }

    /**
     * @return ReadFilerTrait|Filer
     */
    protected function fBeforeReading()
    {
        if ($this->fNotRead) {
            $this->fNotRead = false;
        }
        ++$this->fReadCounter;
        return $this;
    }

    protected function fAfterReading($read)
    {
        $this->fEndReadingIfEof();
        return $read;
    }

    protected function fReadThrowExceptionMessage()
    {
        return static::__transErrorWithModule('read_count', ['count' => $this->fReadCounter]);
    }

    protected function fReadThrowException()
    {
        throw new AppException($this->fReadThrowExceptionMessage());
    }

    protected function fReadTransformExceptionMessage($message)
    {
        return static::__transErrorWithModule('read', [
            'message' => $message,
            'count' => $this->fReadCounter,
        ]);
    }

    public function fReadAll(callable $callback = null, callable $afterCallback = null)
    {
        $reads = [];
        while (($read = $this->fRead()) !== false) {
            if ($read === null) continue;
            $reads[] = $callback ? $callback($read) : $read;
        }
        return $afterCallback ? $afterCallback($reads) : $reads;
    }

    public function fReadWhole(callable $callback = null, callable $beforeCallback = null, callable $afterCallback = null)
    {
        $reads = [];
        while (($read = $this->fRead()) !== false) {
            if ($read === null) continue;
            $reads[] = $read;
        }
        if ($beforeCallback) {
            $reads = $beforeCallback($reads);
        }
        if ($callback) {
            $this->fReadCounter = 0;
            foreach ($reads as $key => &$data) {
                ++$this->fReadCounter;
                try {
                    $data = $callback($data, $this->fReadCounter);
                } catch (\Exception $exception) {
                    if ($this->fIsExcludedException($exception)) {
                        throw ($exception instanceof Exception ? $exception : AppException::from($exception))
                            ->transformMessage(function ($message) {
                                return $this->fReadTransformExceptionMessage($message);
                            });
                    }
                    $this->fReadThrowException();
                }
            }
        }
        return $afterCallback ? $afterCallback($reads) : $reads;
    }
}
