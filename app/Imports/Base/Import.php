<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Imports\Base;

use App\Utils\ExecutionTimeTrait;
use App\Utils\HandledFiles\Filer\Filer;
use App\Utils\ValidationTrait;

abstract class Import
{
    use ValidationTrait, ExecutionTimeTrait;

    /**
     * @var Filer
     */
    protected $filer;

    protected $deleteAfterImporting = true;

    protected $imported = 0;

    public function __construct($file)
    {
        $this->toFiler($file);
    }

    protected function getFilerClass()
    {
        return Filer::class;
    }

    protected function imported()
    {
        ++$this->imported;
        return $this;
    }

    public function count()
    {
        return $this->imported;
    }

    /**
     * @return Filer
     */
    private function getFiler()
    {
        $filerClass = $this->getFilerClass();
        return new $filerClass;
    }

    private function toFiler($file)
    {
        $this->filer = $this->getFiler()->fromExisted($file);
    }

    public abstract function importing();

    public function import()
    {
        $this->importing();
        if ($this->deleteAfterImporting) {
            $this->filer->delete();
        }
        return $this;
    }
}
