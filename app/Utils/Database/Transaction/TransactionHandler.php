<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Database\Transaction;

use Illuminate\Support\Facades\DB;

class TransactionHandler
{
    /**
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connector;

    public function __construct($connection)
    {
        $this->setConnection($connection);
    }

    public function setConnection($connection)
    {
        $this->connector = DB::connection($connection);
        return $this;
    }

    public function begin($options = [])
    {
        $this->connector->beginTransaction();
        return $this;
    }

    public function commit()
    {
        $this->connector->commit();
        return $this;
    }

    public function rollBack()
    {
        $this->connector->rollBack();
        return $this;
    }
}