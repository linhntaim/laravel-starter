<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Database\Transaction;

use App\Utils\Database\MySqlTableLocks;

class MySqlTransactionHandler extends TransactionHandler
{
    public const TRANSACTION_SCOPE_GLOBAL = 'GLOBAL';
    public const TRANSACTION_SCOPE_SESSION = 'SESSION';

    public const ISOLATION_LEVEL_REPEATABLE_READ = 'REPEATABLE READ';
    public const ISOLATION_LEVEL_READ_COMMITTED = 'READ COMMITTED';
    public const ISOLATION_LEVEL_READ_UNCOMMITTED = 'READ UNCOMMITTED';
    public const ISOLATION_LEVEL_SERIALIZABLE = 'SERIALIZABLE';

    public const ACCESS_MODE_READ_WRITE = 'READ WRITE';
    public const ACCESS_MODE_READ_ONLY = 'READ ONLY';

    public const TRANSACTION_SCOPES = [
        MySqlTransactionHandler::TRANSACTION_SCOPE_GLOBAL,
        MySqlTransactionHandler::TRANSACTION_SCOPE_SESSION,
    ];

    public const ISOLATION_LEVELS = [
        MySqlTransactionHandler::ISOLATION_LEVEL_REPEATABLE_READ,
        MySqlTransactionHandler::ISOLATION_LEVEL_READ_COMMITTED,
        MySqlTransactionHandler::ISOLATION_LEVEL_READ_UNCOMMITTED,
        MySqlTransactionHandler::ISOLATION_LEVEL_SERIALIZABLE,
    ];

    public const ACCESS_MODES = [
        MySqlTransactionHandler::ACCESS_MODE_READ_WRITE,
        MySqlTransactionHandler::ACCESS_MODE_READ_ONLY,
    ];

    /**
     * @var MySqlTableLocks
     */
    protected $locks;

    public function begin($options = [])
    {
        return isset($options['locks'][0]) ?
            $this->beginWithLocks($options['locks'], $options) : $this->beginWithoutLocks($options);
    }

    protected function beginWithoutLocks($options = [])
    {
        $isolationLevel = $options['isolation_level'] ?? null;
        $accessMode = $options['access_mode'] ?? null;
        $transactionScope = $options['transaction_scope'] ?? null;

        if (in_array($isolationLevel, static::ISOLATION_LEVELS) || in_array($accessMode, self::ACCESS_MODES)) {
            $transactionScope = in_array($transactionScope, static::TRANSACTION_SCOPES) ? $transactionScope . ' ' : '';
            $isolationLevel = in_array($isolationLevel, static::ISOLATION_LEVELS) ? ' ISOLATION LEVEL ' . $isolationLevel : '';
            $accessMode = in_array($accessMode, static::ACCESS_MODES) ? ' ' . $accessMode : '';
            $this->connector->unprepared(
                sprintf('SET %sTRANSACTION%s%s', $transactionScope, $isolationLevel, $accessMode)
            );
        }
        return parent::begin($options);
    }

    protected function beginWithLocks($locks, $options = [])
    {
        $this->locks = new MySqlTableLocks($locks);
        $this->connector->unprepared('SET autocommit = 0');
        $this->connector->unprepared($this->locks->toLockQuery());
        return $this;
    }

    protected function unlock()
    {
        $this->connector->unprepared($this->locks->toUnlockQuery());
        $this->connector->unprepared('SET autocommit = 1');
        return $this;
    }

    public function commit()
    {
        return is_null($this->locks) ?
            $this->commitWithoutLocks() : $this->commitWithLocks();
    }

    protected function commitWithLocks()
    {
        $this->connector->unprepared('commit');
        return $this->unlock();
    }

    protected function commitWithoutLocks()
    {
        return parent::commit();
    }

    public function rollBack()
    {
        return is_null($this->locks) ?
            $this->rollBackWithoutLocks() : $this->rollBackWithLocks();
    }

    protected function rollBackWithLocks()
    {
        $this->connector->unprepared('rollback');
        return $this->unlock();
    }

    protected function rollBackWithoutLocks()
    {
        return parent::rollBack();
    }
}
