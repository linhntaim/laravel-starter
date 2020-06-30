<?php

namespace App\Utils;

use Illuminate\Support\Facades\DB;

use Throwable;

class TransactionHelper
{
    const ISOLATION_LEVEL_REPEATABLE_READ = 'REPEATABLE READ';
    const ISOLATION_LEVEL_READ_COMMITTED = 'READ COMMITTED';
    const ISOLATION_LEVEL_READ_UNCOMMITTED = 'READ UNCOMMITTED';
    const ISOLATION_LEVEL_SERIALIZABLE = 'SERIALIZABLE';

    const ISOLATION_LEVELS = [
        TransactionHelper::ISOLATION_LEVEL_REPEATABLE_READ,
        TransactionHelper::ISOLATION_LEVEL_READ_COMMITTED,
        TransactionHelper::ISOLATION_LEVEL_READ_UNCOMMITTED,
        TransactionHelper::ISOLATION_LEVEL_SERIALIZABLE,
    ];

    protected static $instance;

    /**
     * @return TransactionHelper
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new TransactionHelper();
        }
        return static::$instance;
    }

    private $currentTransactionConnections = [];
    private $laterThrow = [];
    private $hasTransaction = false;

    private function __construct()
    {
        $this->currentTransactionConnections = [];
        $this->hasTransaction = false;
    }

    public function start($connection = null, $isolationLevel = null)
    {
        if (empty($connection)) {
            $connection = config('database.default');
        }
        if (!in_array($connection, $this->currentTransactionConnections)) {
            $connector = DB::connection($connection);
            if ($connection == 'mysql') {
                if (in_array($isolationLevel, static::ISOLATION_LEVELS)) {
                    $connector->getPdo()->exec('SET SESSION TRANSACTION ISOLATION LEVEL ' . $isolationLevel);
                }
            }
            $connector->beginTransaction();
            $this->currentTransactionConnections[] = $connection;
            $this->hasTransaction = true;
        }
    }

    public function laterThrow(Throwable $throwable, $connection = null)
    {
        if (empty($connection)) {
            $connection = config('database.default');
        }
        $this->laterThrow[$connection] = $throwable;
    }

    public function complete()
    {
        if (!$this->hasTransaction) return;
        foreach ($this->currentTransactionConnections as $connection) {
            DB::connection($connection)->commit();
            $this->throw($connection);
        }
        $this->currentTransactionConnections = [];
        $this->hasTransaction = false;
    }

    protected function throw($connection)
    {
        if (isset($this->laterThrow[$connection])) {
            throw $this->laterThrow[$connection];
        }
    }

    public function stop()
    {
        if (!$this->hasTransaction) return;
        foreach ($this->currentTransactionConnections as $connection) {
            DB::connection($connection)->rollBack();
        }
        $this->currentTransactionConnections = [];
        $this->hasTransaction = false;
    }
}
