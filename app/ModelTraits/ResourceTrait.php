<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

/**
 * Trait ResourceTrait
 * @package App\ModelTraits
 * @property string $resourceClass
 */
trait ResourceTrait
{
    public function getResourceClass()
    {
        return $this->resourceClass;
    }
}
