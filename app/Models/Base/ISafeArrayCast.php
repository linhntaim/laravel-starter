<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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