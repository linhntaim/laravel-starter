<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Imports\Base;

use App\Utils\ClassTrait;
use App\Utils\ExecutionTimeTrait;
use App\Utils\HandledFiles\File;
use App\Utils\HandledFiles\Filer\Filer;
use App\Utils\ValidationTrait;
use App\Vendors\Illuminate\Support\Facades\App;
use Illuminate\Http\UploadedFile;

abstract class Import
{
    use ClassTrait, ValidationTrait, ExecutionTimeTrait;

    public const NAME = 'import';

    /**
     * @var Filer
     */
    protected $filer;

    protected $imported = 0;

    /**
     * Import constructor.
     * @param UploadedFile|File|string|null $file
     */
    public function __construct($file = null)
    {
        if (!is_null($file)) {
            $this->setFile($file);
        }
    }

    public function getName()
    {
        return static::NAME;
    }

    /**
     * @param UploadedFile|File|string $file
     * @return static
     */
    public function setFile($file)
    {
        return $this->toFiler($file);
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

    /**
     * @param UploadedFile|File|string $file
     * @return static
     * @throws
     */
    private function toFiler($file)
    {
        $this->filer = $this->getFiler()->fromExisted($file);
        return $this;
    }

    public abstract function importing();

    public function import()
    {
        if (!is_null($this->filer)) {
            App::benchFrom('import::' . static::class);
            $this->importing();
            $this->filer->delete();
            App::bench('import::' . static::class);
        }
        return $this;
    }
}
