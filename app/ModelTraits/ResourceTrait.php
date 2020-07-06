<?php

namespace App\ModelTraits;

trait ResourceTrait
{
    protected $resourceClass;

    public function getResourceClass()
    {
        return $this->resourceClass;
    }
}