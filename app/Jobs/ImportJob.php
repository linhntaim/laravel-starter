<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Jobs;

use App\Exceptions\AppException;
use App\Exceptions\Exception;
use App\Imports\Base\Import;
use App\Jobs\Base\Job;
use App\ModelRepositories\DataImportRepository;
use App\Models\DataImport;
use App\Utils\HandledFiles\Storage\PrivateStorage;
use Throwable;

class ImportJob extends Job
{
    /**
     * @var DataImport
     */
    public $dataImport;

    /**
     * @var Import
     */
    public $import;

    public function __construct(DataImport $dataImport, Import $import)
    {
        parent::__construct();

        $this->dataImport = $dataImport;
        $this->import = $import;
    }

    public function go()
    {
        $filer = $this->dataImport->file->filer->cloneToPrivate(false, false);
        $storage = $filer->getOriginStorage();
        if ($storage instanceof PrivateStorage) {
            $storage->decrypt();
            $this->import->setFile($storage->getRealPath())->import();
            (new DataImportRepository($this->dataImport))->updateWithAttributes([
                'state' => DataImport::STATE_IMPORTED,
            ]);
            return;
        }

        throw new AppException('Storage has been not supported.');
    }

    public function failed(Throwable $e)
    {
        (new DataImportRepository($this->dataImport))->updateWithAttributes([
            'state' => DataImport::STATE_FAILED,
            'exception' => Exception::toArrayFrom($e),
        ]);
    }
}
