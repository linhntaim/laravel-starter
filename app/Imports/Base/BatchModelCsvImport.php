<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Imports\Base;

use Throwable;

/**
 * Class ModelCsvImport
 * @package App\Imports\Base
 */
abstract class BatchModelCsvImport extends ModelCsvImport
{
    protected $writeBatch = 1000;
    protected $writeIgnored = false;
    protected $written = true;

    protected function csvBeforeImporting($reads)
    {
        $this->modelRepository->batchInsertStart($this->writeBatch, $this->writeIgnored);
    }

    protected function csvAfterImporting($reads)
    {
        if (!$this->written) {
            try {
                $this->modelRepository->batchInsertEnd();
                $this->transactionComplete();
            } catch (Throwable $e) {
                $this->transactionStop();

                $this->csvHandleImportingException($e);
            }
        }
    }

    protected function modelImporting($read, $counter)
    {
        $this->written = $this->modelRepository
            ->batchInsert($read)
            ->batchInserted();
        return null;
    }

    protected function csvImporting($read, $counter)
    {
        if ($this->written) {
            $this->transactionStart();
        }
        try {
            if (!empty($validatedRules = $this->validatedRules())) {
                $this->validatedData($read, $validatedRules, $this->validatedMessages());
            }

            $this->modelImporting($read, $counter);
            if ($this->written) {
                $this->transactionComplete();
            }
        } catch (Throwable $e) {
            $this->transactionStop();

            $this->csvHandleImportingException($e);
        }

        return $read;
    }
}
