<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exports\Base;

use App\ModelRepositories\Base\ModelRepository;
use App\ModelResources\Base\ModelTransformTrait;

abstract class ModelCsvExport extends CsvExport
{
    use ModelTransformTrait;

    public const NAME = 'model';

    protected $readBatch = 1000;

    /**
     * @var ModelRepository
     */
    protected $modelRepository;

    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = $this->modelRepository();
        if ($modelResourceClass = $this->modelResourceClass()) {
            $this->setFixedModelResourceClass($modelResourceClass, $this->modelRepository->modelClass());
        }
    }

    protected abstract function query();

    /**
     * @return string
     */
    protected abstract function modelRepositoryClass();

    /**
     * @return ModelRepository|null
     */
    private function modelRepository()
    {
        $modelRepositoryClass = $this->modelRepositoryClass();
        return $modelRepositoryClass ? new $modelRepositoryClass() : null;
    }

    protected function modelResourceClass()
    {
        return null;
    }

    protected function csvExporting()
    {
        $this->modelRepository->batchReadStart($this->query(), $this->readBatch);
        $this->resetExecutionTime();
        while (($models = $this->modelRepository->batchRead($length, $shouldEnd)) && $length > 0) {
            $this->csvStore($this->modelTransform($models));
            $this->resetExecutionTime();
            if ($shouldEnd) {
                break;
            }
        }
        $this->modelRepository->batchReadEnd();
        return $this;
    }
}
