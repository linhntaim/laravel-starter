<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exports\Base;

use App\ModelRepositories\HandledFileRepository;
use App\Models\HandledFile;
use App\Utils\HandledFiles\Filer\CsvFiler;

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
        $this->csvFiler->fromCreating($this->getName());
        if (!empty($headers = $this->csvHeaders())) {
            $this->csvFiler->fStartAppending()
                ->fWrite([$headers])
                ->fEndWriting();
        }
        return $this;
    }

    protected function csvAfterExporting()
    {
        $this->csvFiler->moveToPublic(false);
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
        $this->csvBeforeExporting()
            ->csvExporting()
            ->csvAfterExporting();
        return $this->handledFileRepository->createWithFiler($this->csvFiler);
    }
}
