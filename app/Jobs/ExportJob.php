<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Jobs;

use App\Exports\Base\Export;
use App\Jobs\Base\Job;
use App\ModelRepositories\DataExportRepository;
use App\Models\DataExport;
use Throwable;

class ExportJob extends Job
{
    /**
     * @var DataExport
     */
    public $dataExport;

    /**
     * @var Export
     */
    public $export;

    public function __construct(DataExport $dataExport, Export $export)
    {
        parent::__construct();

        $this->dataExport = $dataExport;
        $this->export = $export;
    }

    public function go()
    {
        $handledFile = $this->export->export();
        (new DataExportRepository($this->dataExport))->updateWithAttributes([
            'file_id' => $handledFile->id,
            'state' => DataExport::STATE_EXPORTED,
        ]);
    }

    public function failed(Throwable $e)
    {
        (new DataExportRepository($this->dataExport))->updateWithAttributes([
            'state' => DataExport::STATE_FAILED,
        ]);
    }
}
