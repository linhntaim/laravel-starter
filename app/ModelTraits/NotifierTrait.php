<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

trait NotifierTrait
{
    public static function findByKey($key)
    {
        $query = static::query();
        return (method_exists($query, 'withTrashed') ? $query->withTrashed() : $query)->find($key);
    }
}