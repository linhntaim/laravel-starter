<?php

namespace App\Utils\Files\FileReader;

use App\Exceptions\AppException;
use App\Utils\StringHelper;
use Exception as BaseException;

class CsvReader extends FileReader
{
    protected $readLength;
    protected $matchedHeaders;
    protected $extraHeaders;

    public function setReadLength($readLength)
    {
        $this->readLength = $readLength;
        return $this;
    }

    public function setMatchedHeaders(array $matchHeaders, array $extraHeaders = [])
    {
        $this->matchedHeaders = $matchHeaders;
        $this->extraHeaders = $extraHeaders;
        return $this;
    }

    protected function matchesHeaders($read)
    {
        if (empty($this->matchedHeaders)) return $read;
        $countRead = count($read);
        $countMatchedHeaders = count($this->matchedHeaders);
        $countExtraHeaders = count($this->extraHeaders);
        if ($countExtraHeaders <= 0) {
            if ($countRead != $countMatchedHeaders) {
                $this->throwReadException();
            }
        } else {
            if ($countRead < $countMatchedHeaders || $countRead > $countExtraHeaders + $countMatchedHeaders) {
                $this->throwReadException();
            }
        }
        $r = [];
        $headers = [];
        array_push($headers, ...$this->matchedHeaders, ...$this->extraHeaders);
        foreach ($headers as $key => $header) {
            $r[$header] = isset($read[$key]) ? $read[$key] : null;
        }
        return $r;
    }

    protected function throwReadException()
    {
        throw new AppException(static::__transErrorWithModule('read_line', ['line' => $this->readCounter]));
    }

    protected function trulyRead(callable $callback = null)
    {
        $read = fgetcsv($this->handler, $this->readLength);
        if ($read === false) {
            $this->throwReadException();
        }
        array_walk($read, function (&$read) {
            $read = StringHelper::toUtf8($read);
        });
        $read = $this->matchesHeaders($read);
        if ($callback) {
            try {
                $read = $callback($read);
            } catch (BaseException $exception) {
                if ($this->useCustomException) {
                    throw $exception;
                }
                $this->throwReadException();
            }
        }
        return $read;
    }
}
