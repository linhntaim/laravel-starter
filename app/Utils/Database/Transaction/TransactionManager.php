<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Database\Transaction;

use Throwable;

class TransactionManager
{
    protected static $instance;

    /**
     * @return TransactionManager
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new TransactionManager();
        }
        return static::$instance;
    }

    /**
     * @var TransactionHandler[]|array
     */
    private $transactionHandlers;

    private $laterThrow;

    private $hasTransaction;

    private function __construct()
    {
        $this->reset();
    }

    protected function reset()
    {
        $this->transactionHandlers = [];
        $this->laterThrow = [];
        $this->hasTransaction = false;
    }

    /**
     * @param TransactionHandler|null $connection
     * @return MySqlTransactionHandler|TransactionHandler|null
     */
    protected function getTransactionHandler(&$connection = null)
    {
        if (empty($connection)) {
            $connection = config('database.default');
        }
        if (isset($this->transactionHandlers[$connection])) {
            return null;
        }
        switch ($connection) {
            case 'mysql':
                return new MySqlTransactionHandler($connection);
            default:
                return new TransactionHandler($connection);
        }
    }

    public function start($connection = null, $options = [])
    {
        if ($transactionHandler = $this->getTransactionHandler($connection)) {
            $this->transactionHandlers[$connection] = $transactionHandler->begin($options);
            $this->hasTransaction = true;
        }
        return $this;
    }

    public function throwLater(Throwable $throwable, $connection = null)
    {
        if (empty($connection)) {
            $connection = config('database.default');
        }
        $this->laterThrow[$connection] = $throwable;
        return $this;
    }

    public function complete()
    {
        if ($this->hasTransaction) {
            foreach ($this->transactionHandlers as $connection => $transactionHandler) {
                $transactionHandler->commit();
                $this->throw($connection);
            }
            $this->reset();
        }
        return $this;
    }

    protected function throw($connection)
    {
        if (isset($this->laterThrow[$connection])) {
            throw $this->laterThrow[$connection];
        }
        return $this;
    }

    public function stop()
    {
        if ($this->hasTransaction) {
            foreach ($this->transactionHandlers as $transactionHandler) {
                $transactionHandler->rollBack();
            }
            $this->reset();
        }
        return $this;
    }
}
