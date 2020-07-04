<?php

namespace App\ModelResources;

use App\ModelResources\Base\ModelResourceCollection;

class RoleResourceCollection extends ModelResourceCollection
{
    public $collects = RoleResource::class;
}