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
    private $temporaryModelResourceClass;

    /**
     * @var array
     */
    private $temporaryModelResourceClasses = [];

    /**
     * @var string
     */
    private $fixedModelResourceClass;

    /**
     * @var array
     */
    private $fixedModelResourceClasses = [];

    protected function setTemporaryModelResourceClass($modelResourceClass, $modelClass = null)
    {
        if (is_null($modelClass)) {
            $this->temporaryModelResourceClass = $modelResourceClass;
        } else {
            if (is_null($modelResourceClass)) {
                unset($this->temporaryModelResourceClasses[$modelClass]);
            } else {
                $this->temporaryModelResourceClasses[$modelClass] = $modelResourceClass;
            }
        }
        return $this;
    }

    protected function setFixedModelResourceClass($modelResourceClass, $modelClass = null)
    {
        if (is_null($modelClass)) {
            $this->fixedModelResourceClass = $modelResourceClass;
        } else {
            if (is_null($modelResourceClass)) {
                unset($this->fixedModelResourceClasses[$modelClass]);
            } else {
                $this->fixedModelResourceClasses[$modelClass] = $modelResourceClass;
            }
        }
        return $this;
    }

    protected function setModelResourceClass($modelResourceClass, $fixed = false, $modelClass = null)
    {
        return $fixed ?
            $this->setTemporaryModelResourceClass($modelResourceClass, $modelClass)
            : $this->setFixedModelResourceClass($modelResourceClass, $modelClass);
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
        $modelResourceClass = $this->getModelResourceClass(
            $model instanceof Model ?
                $model : ($model->count() > 0 ? ($model instanceof Collection ? $model->first() : $model->items()[0]) : null)
        );
        return $model instanceof Collection || $model instanceof LengthAwarePaginator ?
            $modelResourceClass::collection($model)
            : ($model instanceof Model ? new $modelResourceClass($model) : null);
    }

    /**
     * @param Model $model
     * @return string
     */
    private function getModelResourceClass($model)
    {
        if (!is_null($this->temporaryModelResourceClass)) {
            $modelResourceClass = $this->temporaryModelResourceClass;
            $this->setTemporaryModelResourceClass(null);
            return $modelResourceClass;
        }
        if (!is_null($this->fixedModelResourceClass)) {
            return $this->fixedModelResourceClass;
        }
        if ($model) {
            $modelClass = get_class($model);
            if (isset($this->temporaryModelResourceClasses[$modelClass])) {
                $modelResourceClass = $this->temporaryModelResourceClasses[$modelClass];
                $this->setTemporaryModelResourceClass(null, $modelClass);
                return $modelResourceClass;
            }
            if (isset($this->fixedModelResourceClasses[$modelClass])) {
                return $this->fixedModelResourceClasses[$modelClass];
            }
            if ($model instanceof IResource) {
                $modelResourceClass = $model->getResourceClass();
                if ($modelResourceClass) return $modelResourceClass;
            }
        }

        return ModelResource::class;
    }
}