<?php

namespace App\ModelTraits;

trait SafeArrayCastTrait
{
    protected $safeArrayCastEnabled = [];

    /**
     * @return static
     */
    public function disableSafeArrayCast(string $attributeName)
    {
        $this->safeArrayCastEnabled[$attributeName] = false;
        return $this;
    }

    /**
     * @return static
     */
    public function enableSafeArrayCast(string $attributeName)
    {
        unset($this->safeArrayCastEnabled[$attributeName]);
        return $this;
    }

    /**
     * @return bool
     */
    public function isSafeArrayCastEnabled(string $attributeName)
    {
        return !isset($this->safeArrayCastEnabled[$attributeName]);
    }
}