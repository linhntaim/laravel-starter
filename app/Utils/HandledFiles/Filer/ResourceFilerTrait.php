<?php

namespace App\Utils\HandledFiles\Filer;

use App\Utils\HandledFiles\Storage\LocalStorage;

trait ResourceFilerTrait
{
    protected $fResource = null;

    /**
     * @return LocalStorage|null
     */
    protected function fHandled()
    {
        return ($originStorage = $this->getOriginStorage()) && $originStorage instanceof LocalStorage ? $originStorage : null;
    }

    /**
     * @param string $mode
     * @return Filer|mixed
     */
    public function fOpen($mode = Filer::MODE_WRITE_FRESH)
    {
        if (($originStorage = $this->fHandled()) && is_null($this->fResource)) {
            $this->fResource = fopen($originStorage->getRealPath(), $mode);
        }
        return $this;
    }

    public function fClose()
    {
        if (is_resource($this->fResource)) {
            fclose($this->fResource);
            $this->fResource = null;
        }
        return $this;
    }
}
