<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Imports\Base;

use App\Utils\HandledFiles\Filer\CsvFiler;

/**
 * Class CsvImport
 * @package App\Imports\Base
 * @property CsvFiler $filer
 */
abstract class CsvImport extends Import
{
    protected $customExceptionDisabled = true;
    protected $headerSkipped = true;
    protected $excludedCustomExceptions = [];

    protected function getFilerClass()
    {
        return CsvFiler::class;
    }

    protected function csvHeaders()
    {
        return [];
    }

    protected function csvExtraHeaders()
    {
        return [];
    }

    protected function csvBeforeImporting($reads)
    {
    }

    protected function csvAfterImporting($reads)
    {
    }

    protected function csvImporting($read, $counter)
    {
        return $read;
    }

    public function importing()
    {
        $this->csvRead();
        $this->csvImport();
    }

    protected function csvRead()
    {
        if ($this->customExceptionDisabled) {
            $this->filer->fReadDisableCustomException($this->excludedCustomExceptions);
        }
        if (!empty($headers = $this->csvHeaders())) {
            $this->filer->fReadSetMatchedHeaders($headers, $this->csvExtraHeaders());
        }
        $this->filer->fReadSkipHeader($this->headerSkipped)
            ->fStartReading();
    }

    protected abstract function csvImport();
}
