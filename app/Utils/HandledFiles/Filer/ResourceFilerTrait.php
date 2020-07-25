<?php

namespace App\Utils\HandledFiles\Filer;

use App\Utils\HandledFiles\Storage\LocalStorage;

trait ResourceFilerTrait
{
    protected $fResource = null;

    protected $fReadAndWriteEnabled = false;
    protected $fBinaryEnabled = false;
    protected $fTextModeTranslationEnabled = false;

    /**
     * @param bool $enabled
     * @return Filer|mixed
     * @return $this
     */
    public function fEnableBothReadingAndWriting($enabled = true)
    {
        $this->fReadAndWriteEnabled = $enabled;
        return $this;
    }

    /**
     * @param bool $enabled
     * @return Filer|mixed
     * @return $this
     */
    public function fEnableBinaryHandling($enabled = true)
    {
        $this->fBinaryEnabled = $enabled;
        return $this;
    }

    /**
     * @param bool $enabled
     * @return Filer|mixed
     * @return $this
     */
    public function fEnableTextModeTranslation($enabled = true)
    {
        $this->fTextModeTranslationEnabled = $enabled;
        return $this;
    }

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
    public function fOpen($mode = Filer::MODE_WRITE)
    {
        if (($originStorage = $this->fHandled()) && is_null($this->fResource)) {
            $this->fResource = fopen($originStorage->getRealPath(), implode('', [
                $mode,
                $this->fReadAndWriteEnabled ? '+' : '',
                $this->fBinaryEnabled ? 'b' : '',
                $this->fTextModeTranslationEnabled ? 't' : '',
            ]));
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
