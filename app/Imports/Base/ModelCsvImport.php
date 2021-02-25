<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Imports\Base;

use App\ModelRepositories\Base\ModelRepository;
use App\Models\Base\Model;
use App\Utils\TransactionTrait;

/**
 * Class ModelCsvImport
 * @package App\Imports\Base
 */
abstract class ModelCsvImport extends CsvImport
{
    use TransactionTrait;

    /**
     * @var ModelRepository
     */
    protected $modelRepository;

    public function __construct($file)
    {
        parent::__construct($file);

        $this->modelRepository = $this->modelRepository();
    }

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
        return new $modelRepositoryClass();
    }

    protected function validatedRules()
    {
        return [];
    }

    protected function validatedMessages()
    {
        return [];
    }

    /**
     * @param $read
     * @param $counter
     * @return Model
     * @throws
     */
    protected function modelImporting($read, $counter)
    {
        return $this->modelRepository->createWithAttributes($read);
    }

    /**
     * @param $read
     * @param $counter
     * @return mixed
     * @throws
     */
    protected function csvImporting($read, $counter)
    {
        $this->transactionStart();
        try {
            if (!empty($validatedRules = $this->validatedRules())) {
                $this->validatedData($read, $validatedRules, $this->validatedMessages());
            }

            $this->modelImporting($read, $counter);
            $this->transactionComplete();
        } catch (\Exception $exception) {
            $this->transactionStop();

            $this->csvHandleImportingException($exception);
        }

        return $read;
    }

    protected function csvHandleImportingException($exception)
    {
        throw $exception;
    }
}
