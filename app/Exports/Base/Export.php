<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exports\Base;

use App\ModelRepositories\HandledFileRepository;
use App\Models\HandledFile;

abstract class Export
{
    const NAME = 'export';

    /**
     * @var HandledFileRepository
     */
    protected $handledFileRepository;

    public function __construct()
    {
        $this->handledFileRepository = new HandledFileRepository();
    }

    public function getName()
    {
        return static::NAME;
    }

    /**
     * @return HandledFile
     */
    public abstract function export();
}
