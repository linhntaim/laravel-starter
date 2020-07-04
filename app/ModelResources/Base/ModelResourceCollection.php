<?php

namespace App\ModelResources\Base;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ModelResourceCollection extends ResourceCollection
{
    public $preserveKeys = true;

    public static $wrap = 'models';
}