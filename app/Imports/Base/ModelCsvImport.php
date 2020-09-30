<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Imports\Base;

use App\ModelRepositories\Base\ModelRepository;
use App\Models\Base\Model;
use App\Utils\TransactionHelper;
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

    protected function validatedRules()
    {
        return [];
    }

    /**
     * @param $read
     * @return Model
     * @throws
     */
    protected function modelImporting($read)
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
        $this->transactionStart(null, TransactionHelper::ISOLATION_LEVEL_READ_COMMITTED);
        try {
            if (!empty($validatedRules = $this->validatedRules())) {
                $this->validatedData($read, $validatedRules);
            }

            try {
                $this->modelImporting($read);
            } catch (\Exception $exception) {
                if ($this->filer->fIsExcludedException($exception)) {
                    $this->transactionStop();
                    return $read;
                }
                throw $exception;
            }

            $this->transactionComplete();
        } catch (\Exception $exception) {
            $this->transactionStop();

            throw $exception;
        }

        return $read;
    }
}
