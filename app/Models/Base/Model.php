<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

use App\ModelTraits\ModelTrait;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Class Model
 * @package App\Models\Base
 */
class Model extends BaseModel implements IModel
{
    use ModelTrait;

    protected $resourceClass;

    protected $activityLogHidden = [];
}
