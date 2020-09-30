<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources\Base;

use App\Models\Base\IResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\LengthAwarePaginator;

trait ModelTransformTrait
{
    /**
     * @var string
     */
    private $modelResourceClass;

    protected function setModelResourceClass($value)
    {
        $this->modelResourceClass = $value;
        return $this;
    }

    /**
     * @param Model|Collection|LengthAwarePaginator $model
     * @param null $request
     * @param bool $wrapped
     * @return array
     */
    protected function modelTransform($model, $request = null, $wrapped = false)
    {
        if (is_null($model) || $model instanceof MissingValue) return null;

        return $this->getModelResource($model)->enableWrapping($wrapped)->model($request);
    }

    /**
     * @param Model|Collection|LengthAwarePaginator $model
     * @return IModelResource
     */
    private function getModelResource($model)
    {
        $resourceModel = $model instanceof Model ?
            $model : ($model->count() > 0 ? ($model instanceof Collection ? $model->first() : $model->items()[0]) : null);
        $resourceClass = !is_null($this->modelResourceClass) ?
            $this->modelResourceClass
            : ($resourceModel && $resourceModel instanceof IResource ?
                $resourceModel->getResourceClass() : null);
        if (is_null($resourceClass)) {
            $resourceClass = ModelResource::class;
        }
        $modelResource = $model instanceof Collection || $model instanceof LengthAwarePaginator ?
            $resourceClass::collection($model)
            : ($model instanceof Model ? new $resourceClass($model) : null);
        $this->setModelResourceClass(null);
        return $modelResource;
    }
}