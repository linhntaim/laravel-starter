<?php

namespace App\Exports;

use App\Models\ManagedFile;

abstract class Export
{
    const NAME = 'export';

    public function getName()
    {
        return static::NAME;
    }

    /**
     * @return ManagedFile
     */
    public abstract function export();
}
