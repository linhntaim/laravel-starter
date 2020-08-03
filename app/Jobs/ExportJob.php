<?php

namespace App\Jobs;

use App\Exports\Base\Export;
use App\Jobs\Base\Job;
use App\ModelRepositories\DataExportRepository;
use App\Models\DataExport;

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

    public function failed()
    {
        (new DataExportRepository($this->dataExport))->updateWithAttributes([
            'state' => DataExport::STATE_FAILED,
        ]);
    }
}
