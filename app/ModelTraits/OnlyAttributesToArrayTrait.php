<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

trait OnlyAttributesToArrayTrait
{
    public function toArray()
    {
        return $this->attributesToArray();
    }
}