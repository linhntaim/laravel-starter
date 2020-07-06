<?php

namespace App\Jobs;

use App\Exports\Export;
use App\Jobs\Base\Job;
use App\ModelRepositories\DataExportRepository;
use App\Models\DataExport;

class ExportJob extends Job
{
    /**
     * @var DataExport
     */
    public $dataExport;

    public $export;

    public function __construct(DataExport $dataExport, Export $export)
    {
        $this->dataExport = $dataExport;
        $this->export = $export;
    }

    public function go()
    {
        $managedFile = $this->export->export();
        (new DataExportRepository($this->dataExport))->updateWithAttributes([
            'managed_file_id' => $managedFile->id,
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
