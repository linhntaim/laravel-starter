<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Filer;

use App\Exceptions\AppException;
use App\Utils\HandledFiles\Storage\LocalStorage;

trait ResourceFilerTrait
{
    protected $fResource = null;
    protected $fNewly = true;

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
     * @throws
     */
    public function fOpen($mode = Filer::MODE_WRITE)
    {
        if (($originStorage = $this->fHandled()) && !is_resource($this->fResource)) {
            $filePath = $originStorage->getRealPath();
            clearstatcache(); // clear cache before calling filesize
            $this->fNewly = !file_exists($filePath) || filesize($filePath) == 0;
            $this->fResource = fopen($filePath, implode('', [
                $mode,
                $this->fReadAndWriteEnabled ? '+' : '',
                $this->fBinaryEnabled ? 'b' : '',
                $this->fTextModeTranslationEnabled ? 't' : '',
            ]));

            if ($this->fResource === false) {
                throw new AppException('Could not open file');
            }
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
