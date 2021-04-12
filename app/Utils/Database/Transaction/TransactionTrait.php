<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Database\Transaction;

use Throwable;

trait TransactionTrait
{
    protected function transactionManager()
    {
        return TransactionManager::getInstance();
    }

    protected function transactionStart($connection = null, $options = [])
    {
        $this->transactionManager()->start($connection, $options);
        return $this;
    }

    protected function transactionComplete()
    {
        $this->transactionManager()->complete();
        return $this;
    }

    protected function transactionThrow(Throwable $throwable, $connection = null)
    {
        $this->transactionManager()->throwLater($throwable, $connection);
        return $this;
    }

    protected function transactionStop()
    {
        $this->transactionManager()->stop();
        return $this;
    }
}
