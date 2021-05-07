<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exports\Base;

use App\ModelRepositories\HandledFileRepository;
use App\Models\HandledFile;
use App\Utils\HandledFiles\Filer\CsvFiler;
use App\Vendors\Illuminate\Support\Facades\App;

/**
 * Trait CsvExportTrait
 * @package App\Exports\Base
 * @property HandledFileRepository $handledFileRepository
 */
trait CsvExportTrait
{
    /**
     * @var CsvFiler
     */
    protected $csvFiler;

    public function csvHeaders()
    {
        return [];
    }

    protected function csvBeforeExporting()
    {
        $this->csvFiler = new CsvFiler();
        $this->csvFiler->fromCreating($this->getName(), 'csv', null); // create at root directory of private storage
        if (!empty($headers = $this->csvHeaders())) {
            $this->csvFiler->fStartAppending()
                ->fWrite([$headers])
                ->fEndWriting();
        }
        return $this;
    }

    protected function csvAfterExporting()
    {
        $this->csvFiler->moveTo(false); // move to time-based directory of private storage
        return $this;
    }

    protected function csvStore($data)
    {
        $this->csvFiler->fStartAppending()
            ->fWrite($data)
            ->fEndWriting();
        return $this;
    }

    protected function csvExporting()
    {
        return $this;
    }

    /**
     * @return HandledFile
     */
    public function csvExport()
    {
        App::benchFrom('export::csv::' . $this->getName());
        $this->csvBeforeExporting()
            ->csvExporting()
            ->csvAfterExporting();
        return tap(
            $this->handledFileRepository
                ->usePublic() // make sure to move to public storage
                ->createWithFiler($this->csvFiler),
            function () {
                App::bench('export::csv::' . $this->getName());
            }
        );
    }
}
