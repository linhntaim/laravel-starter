<?php

namespace App\Utils;

use Illuminate\Support\Facades\DB;

use Throwable;

class TransactionHelper
{
    const TRANSACTION_SCOPE_GLOBAL = 'GLOBAL';
    const TRANSACTION_SCOPE_SESSION = 'SESSION';

    const ISOLATION_LEVEL_REPEATABLE_READ = 'REPEATABLE READ';
    const ISOLATION_LEVEL_READ_COMMITTED = 'READ COMMITTED';
    const ISOLATION_LEVEL_READ_UNCOMMITTED = 'READ UNCOMMITTED';
    const ISOLATION_LEVEL_SERIALIZABLE = 'SERIALIZABLE';

    const ACCESS_MODE_READ_WRITE = 'READ WRITE';
    const ACCESS_MODE_READ_ONLY = 'READ ONLY';

    const TRANSACTION_SCOPES = [
        TransactionHelper::TRANSACTION_SCOPE_GLOBAL,
        TransactionHelper::TRANSACTION_SCOPE_SESSION,
    ];

    const ISOLATION_LEVELS = [
        TransactionHelper::ISOLATION_LEVEL_REPEATABLE_READ,
        TransactionHelper::ISOLATION_LEVEL_READ_COMMITTED,
        TransactionHelper::ISOLATION_LEVEL_READ_UNCOMMITTED,
        TransactionHelper::ISOLATION_LEVEL_SERIALIZABLE,
    ];

    const ACCESS_MODES = [
        TransactionHelper::ACCESS_MODE_READ_WRITE,
        TransactionHelper::ACCESS_MODE_READ_ONLY,
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

    public function start($connection = null, $isolationLevel = null, $accessMode = null, $transactionScope = null)
    {
        if (empty($connection)) {
            $connection = config('database.default');
        }
        if (!in_array($connection, $this->currentTransactionConnections)) {
            $connector = DB::connection($connection);
            $connector->beginTransaction();
            if ($connection == 'mysql') {
                if (in_array($isolationLevel, static::ISOLATION_LEVELS) || in_array($accessMode, self::ACCESS_MODES)) {
                    $transactionScope = in_array($transactionScope, static::TRANSACTION_SCOPES) ? $transactionScope . ' ' : '';
                    $isolationLevel = in_array($isolationLevel, static::ISOLATION_LEVELS) ? ' ISOLATION LEVEL ' . $isolationLevel : '';
                    $accessMode = in_array($accessMode, static::ACCESS_MODES) ? ' ' . $accessMode : '';
                    $connector->getPdo()->exec(
                        sprintf('SET %sTRANSACTION%s%s', $transactionScope, $isolationLevel, $accessMode)
                    );
                }
            }
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
