<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Database;

class TableLocks
{
    /**
     * @var TableLock[]|array
     */
    protected $locks;

    public function __construct($locks)
    {
        array_walk($locks, function ($lock) {
            $this->addLock($lock);
        });
    }

    public function addLock($lock)
    {
        $this->locks[] = new TableLock($lock);
    }

    public function toLockQuery()
    {
        return null;
    }

    public function toUnlockQuery()
    {
        return null;
    }
}