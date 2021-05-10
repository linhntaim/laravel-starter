<?php

namespace App\Models\Base;

interface ISafeArrayCast
{
    /**
     * @return static
     */
    public function disableSafeArrayCast(string $attributeName);

    /**
     * @return static
     */
    public function enableSafeArrayCast(string $attributeName);

    /**
     * @return bool
     */
    public function isSafeArrayCastEnabled(string $attributeName);
}