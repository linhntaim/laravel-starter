<?php

namespace App\Utils\HandledFiles\Filer;

use App\Exceptions\AppException;
use App\Utils\StringHelper;

class CsvFiler extends Filer
{
    protected $fReadMatchedHeaders = [];
    protected $fReadExtraHeaders = [];
    protected $fReadSkipHeader = true;

    public function fStartReading()
    {
        parent::fStartReading();
        $this->fReadExtraHeaders = [];
        $this->fReadExtraHeaders = [];
        $this->fReadSkipHeader = true;
        return $this;
    }

    public function fReadSetMatchedHeaders(array $matchHeaders = [], array $extraHeaders = [])
    {
        $this->fReadMatchedHeaders = $matchHeaders;
        $this->fReadExtraHeaders = $extraHeaders;
        return $this;
    }

    public function fReadSkipHeader($skipped = true)
    {
        $this->fReadSkipHeader = $skipped;
        return $this;
    }

    /**
     * @param $read
     * @return array
     * @throws AppException
     */
    protected function fReadMatchesHeaders($read)
    {
        if (empty($this->fReadMatchedHeaders)) return $read;
        $countRead = count($read);
        $countMatchedHeaders = count($this->fReadMatchedHeaders);
        $countExtraHeaders = count($this->fReadExtraHeaders);
        if ($countExtraHeaders <= 0) {
            if ($countRead != $countMatchedHeaders) {
                $this->fReadThrowException();
            }
        } else {
            if ($countRead < $countMatchedHeaders || $countRead > $countExtraHeaders + $countMatchedHeaders) {
                $this->fReadThrowException();
            }
        }
        $r = [];
        $headers = [];
        array_push($headers, ...$this->fReadMatchedHeaders, ...$this->fReadExtraHeaders);
        foreach ($headers as $key => $header) {
            $r[$header] = isset($read[$key]) ? $read[$key] : null;
        }
        return $r;
    }

    /**
     * @throws AppException
     */
    protected function fReadThrowException()
    {
        throw new AppException(static::__transErrorWithModule('read_line', ['line' => $this->fReadCounter]));
    }

    /**
     * @return array|null
     * @throws AppException
     */
    protected function fReading()
    {
        $read = fgetcsv($this->fResource, $this->fReadLength);

        if ([null] === $read) {
            return null;
        }

        if ($read === false) {
            if (feof($this->fResource)) {
                return null;
            }
            $this->fReadThrowException();
        }

        if ($this->fReadCounter == 1 && $this->fReadSkipHeader) {
            return null;
        }

        array_walk($read, function (&$item) {
            $item = StringHelper::toUtf8(trim($item, " \t\n\r\0\x0B" . chr(0xEF) . chr(0xBB) . chr(0xBF)));
        });
        return $this->fReadMatchesHeaders($read);
    }

    public function fWrite($contents, $delimiter = ',', $enclosure = '"', $escapeChar = '\\')
    {
        return $this->fWriting($contents, function ($contents) use ($delimiter, $enclosure, $escapeChar) {
            if ($this->fNotWritten) {
                fputs($this->fResource, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            }
            fputcsv($this->fResource, $contents, $delimiter, $enclosure, $escapeChar);
        });
    }

    public function fromCreating($name = null, $extension = 'csv', $toDirectory = null)
    {
        return parent::fromCreating($name, $extension, $toDirectory);
    }
}
