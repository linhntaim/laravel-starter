<?php

namespace App\Models\Base;

use App\Utils\Database\MySqlTableLock;

trait ModelTrait
{
    protected function getTableLock($options = [])
    {
        $connection = $this->getConnectionName() ?? config('database.default');
        $options['table'] = $this->getTable();
        switch ($connection) {
            case 'mysql':
                return new MySqlTableLock($options);
            default:
                return null;
        }
    }

    public function lockTableQuery($options = [])
    {
        if ($tableLock = $this->getTableLock($options)) {
            return $tableLock->toLockQuery();
        }
        return null;
    }

    public function lockTable($options = [])
    {
        if ($query = $this->lockTableQuery($options)) {
            return $this->getConnection()->unprepared($query);
        }
        return false;
    }

    public function unlockTableQuery()
    {
        if ($tableLock = $this->getTableLock()) {
            return $tableLock->toUnlockQuery();
        }
        return null;
    }

    public function unlockTable()
    {
        if ($query = $this->unlockTableQuery()) {
            return $this->getConnection()->unprepared($query);
        }
        return false;
    }
}