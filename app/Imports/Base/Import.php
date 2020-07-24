<?php

namespace App\Imports\Base;

use App\Utils\ClassTrait;
use App\Utils\HandledFiles\Filer\Filer;
use App\Utils\ValidationTrait;

abstract class Import
{
    use ClassTrait, ValidationTrait;

    /**
     * @var Filer
     */
    protected $filer;

    protected $deleteAfterImporting = true;

    public function __construct($file)
    {
        $this->toFiler($file);
    }

    protected function getFilerClass()
    {
        return Filer::class;
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

    public function import()
    {
        if ($this->deleteAfterImporting) {
            $this->filer->delete();
        }
    }
}
