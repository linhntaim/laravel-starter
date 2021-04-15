<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exports\Base;

use App\ModelRepositories\HandledFileRepository;
use App\Models\HandledFile;
use App\Utils\ClassTrait;

abstract class Export
{
    use ClassTrait;

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
