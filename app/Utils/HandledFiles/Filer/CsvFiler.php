<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Filer;

use App\Exceptions\AppException;
use App\Vendors\Illuminate\Support\Str;

class CsvFiler extends Filer
{
    protected $fReadMatchedHeaders = [];

    protected $fReadExtraHeaders = [];

    protected $fReadSkipHeader = true;

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
     * @throws
     */
    protected function fReadMatchesHeaders($read)
    {
        if (empty($this->fReadMatchedHeaders)) {
            return $read;
        }
        $countRead = count($read);
        $countMatchedHeaders = count($this->fReadMatchedHeaders);
        $countExtraHeaders = count($this->fReadExtraHeaders);
        if ($countExtraHeaders <= 0) {
            if ($countRead != $countMatchedHeaders) {
                $this->fReadThrowException();
            }
        }
        else {
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

    protected function fReadThrowExceptionMessage()
    {
        return static::__transErrorWithModule('read_line', ['line' => $this->fReadCounter]);
    }

    protected function fReadTransformExceptionMessage($message)
    {
        return static::__transErrorWithModule('read', [
            'message' => $message,
            'line' => $this->fReadCounter,
        ]);
    }

    public function fReadFirstOnly()
    {
        $fReadSkipHeader = $this->fReadSkipHeader;
        $this->fReadSkipHeader = false;
        $read = parent::fReadFirstOnly();
        $this->fReadSkipHeader = $fReadSkipHeader;
        return $read;
    }

    /**
     * @return array|null
     * @throws
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

        array_walk($read, function (&$item, $index) {
            if ($this->fReadCounter == 1 && $index == 0) {
                $item = ltrim($item, chr(0xEF) . chr(0xBB) . chr(0xBF));
            }
            $item = Str::toUtf8($item);
        });
        return $this->fReadMatchesHeaders($read);
    }

    public function fWrite($contents, $delimiter = ',', $enclosure = '"', $escapeChar = '\\')
    {
        return $this->fWriting($contents, function ($contents) use ($delimiter, $enclosure, $escapeChar) {
            if ($this->fNewly) {
                fputs($this->fResource, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
                $this->fNewly = false;
            }
            fputcsv($this->fResource, $contents, $delimiter, $enclosure, $escapeChar);
        });
    }

    public function fromCreating($name = null, $extension = 'csv', $toDirectory = false)
    {
        return parent::fromCreating($name, $extension, $toDirectory);
    }
}
