<?php

namespace App\Utils\HandledFiles\Filer;

trait WriteFilerTrait
{
    protected $fNotWritten = true;

    /**
     * @return WriteFilerTrait
     */
    public function fStartWriting()
    {
        return $this->fOpen(Filer::MODE_WRITE_FRESH);
    }

    /**
     * @return WriteFilerTrait
     */
    public function fStartAppending()
    {
        return $this->fOpen(Filer::MODE_WRITE_APPEND);
    }

    /**
     * @param $contents
     * @param null $length
     * @return WriteFilerTrait
     */
    public function fWrite($contents, $length = null)
    {
        return $this->fWriting($contents, function ($contents) use ($length) {
            fwrite($this->fResource, $contents, $length);
        });
    }

    /**
     * @param $contents
     * @param callable $callback
     * @return WriteFilerTrait
     */
    protected function fWriting($contents, callable $callback)
    {
        if (!is_null($this->fResource)) {
            $contents = $this->fBeforeWriting($contents);
            foreach ((array)$contents as $content) {
                $callback($content);
            }
            $this->fAfterWriting($contents);
        }
        return $this;
    }

    protected function fBeforeWriting($contents)
    {
        return $contents;
    }

    protected function fAfterWriting($contents)
    {
        $this->fNotWritten = false;
    }

    /**
     * @return WriteFilerTrait
     */
    public function fEndWriting()
    {
        $this->fClose();
        $this->fNotWritten = true;
        return $this;
    }
}
