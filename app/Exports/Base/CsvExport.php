<?php

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
