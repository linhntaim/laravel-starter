<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

interface IProtected
{
    public static function getProtectedKey();

    public static function getProtectedValues();

    public function getProtectedValue();
}
