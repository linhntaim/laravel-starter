<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

trait ResourceTrait
{
    protected $resourceClass;

    public function getResourceClass()
    {
        return $this->resourceClass;
    }
}