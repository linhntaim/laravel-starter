<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

use App\ModelTraits\ActivityLogTrait;
use App\ModelTraits\FromModelTrait;
use App\ModelTraits\OnlyAttributesToArrayTrait;
use App\ModelTraits\ResourceTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel implements IResource, IActivityLog, IFromModel, IModel
{
    use ModelTrait, HasFactory, OnlyAttributesToArrayTrait, ResourceTrait, FromModelTrait, ActivityLogTrait;

    protected $resourceClass;
}
