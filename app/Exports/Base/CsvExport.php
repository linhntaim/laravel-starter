<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exports\Base;

use App\Models\HandledFile;

abstract class CsvExport extends Export implements ICsvExport
{
    use CsvExportTrait;

    /**
     * @return HandledFile
     */
    public function export()
    {
        return $this->csvExport();
    }
}
