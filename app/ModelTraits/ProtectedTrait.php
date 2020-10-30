<?php

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
