<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

use App\ModelTraits\ActivityLogTrait;
use App\ModelTraits\FromModelTrait;
use App\ModelTraits\OnlyAttributesToArrayTrait;
use App\ModelTraits\ResourceTrait;
use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel implements IResource, IActivityLog
{
    use OnlyAttributesToArrayTrait, ResourceTrait, ActivityLogTrait, FromModelTrait;

    protected $resourceClass;
}
