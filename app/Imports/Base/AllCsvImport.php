<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Imports\Base;

use App\Utils\HandledFiles\Filer\CsvFiler;

/**
 * Class AllCsvImport
 * @package App\Imports\Base
 * @property CsvFiler $filer
 */
abstract class AllCsvImport extends CsvImport
{
    protected function csvImport()
    {
        $this->filer->fReadAll(
            function ($read, $counter) {
                $this->resetExecutionTime();
                return $this->csvImporting($read, $counter);
            },
            function ($reads) {
                $this->csvAfterImporting($reads);
                return $reads;
            }
        );
    }
}
