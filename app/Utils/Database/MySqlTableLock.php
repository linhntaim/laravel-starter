<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Database;

class MySqlTableLock extends TableLock
{
    public function getLockType()
    {
        return $this->options['lock_type'] ?? 'WRITE';
    }

    public function toLockQuery()
    {
        return sprintf('LOCK TABLES %s %s', $this->getTable(), $this->getLockType());
    }

    public function toUnlockQuery()
    {
        return 'UNLOCK TABLES';
    }
}
