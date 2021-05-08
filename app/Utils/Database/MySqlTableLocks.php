<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Database;

class MySqlTableLocks extends TableLocks
{
    public function addLock($lock)
    {
        $this->locks[] = new MySqlTableLock($lock);
    }

    public function toLockQuery()
    {
        return 'LOCK TABLES ' . implode(', ', array_map(function (MySqlTableLock $tableLock) {
                return sprintf('%s %s', $tableLock->getTable(), $tableLock->getLockType());
            }, $this->locks));
    }

    public function toUnlockQuery()
    {
        return 'UNLOCK TABLES';
    }
}