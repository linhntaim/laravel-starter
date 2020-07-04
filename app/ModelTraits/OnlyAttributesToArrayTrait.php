<?php

namespace App\ModelTraits;

trait OnlyAttributesToArrayTrait
{
    public function toArray()
    {
        return $this->attributesToArray();
    }
}