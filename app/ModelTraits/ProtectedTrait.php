<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

trait ProtectedTrait
{
    public static function getProtectedValues()
    {
        return static::PROTECTED;
    }

    public function getProtectedValue()
    {
        return $this->{static::getProtectedKey()};
    }

    public function scopeNoneProtected($query)
    {
        return $query->whereNotIn(static::getProtectedKey(), static::getProtectedValues());
    }
}
