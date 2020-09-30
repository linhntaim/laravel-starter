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
class CsvImport extends Import
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

    public function import()
    {
        $this->csvImport();
        parent::import();
    }

    protected function csvImport()
    {
        if ($this->customExceptionDisabled) {
            $this->filer->fReadDisableCustomException($this->excludedCustomExceptions);
        }
        if (!empty($headers = $this->csvHeaders())) {
            $this->filer->fReadSetMatchedHeaders($headers);
        }
        $this->filer->fReadSkipHeader($this->headerSkipped)
            ->fStartReading()
            ->fReadWhole(
                function ($read, $counter) {
                    $this->csvImporting($read, $counter);
                },
                function ($reads) {
                    $this->csvBeforeImporting($reads);
                    return $reads;
                },
                function ($reads) {
                    $this->csvAfterImporting($reads);
                    return $reads;
                }
            );
    }
}
