<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Database;

class TableLock
{
    /**
     * @var array
     */
    protected $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->options['table'] ?? null;
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
