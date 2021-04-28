<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Imports\Base;

use App\Utils\HandledFiles\Filer\CsvFiler;

/**
 * Class WholeCsvImport
 * @package App\Imports\Base
 * @property CsvFiler $filer
 */
abstract class WholeCsvImport extends CsvImport
{
    protected function csvImport()
    {
        $this->filer->fReadWhole(
            function ($read, $counter) {
                $this->resetExecutionTime();
                return $this->csvImporting($read, $counter);
            },
            function ($reads) {
                $this->resetExecutionTime();
                $this->csvBeforeImporting($reads);
                return $reads;
            },
            function ($reads) {
                $this->resetExecutionTime();
                $this->csvAfterImporting($reads);
                return $reads;
            }
        );
    }
}
