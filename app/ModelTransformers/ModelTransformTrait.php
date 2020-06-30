<?php

namespace App\ModelTransformers;

use App\Utils\Helper;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ModelTransformTrait
{
    /**
     * @param string $modelTransformerClass
     * @param LengthAwarePaginator|Model|Collection|iterable $model
     * @param array $context
     * @param Closure|null $mapping
     * @return array
     */
    protected function modelTransform($modelTransformerClass, $model, $context = [], $mapping = null)
    {
        return $this->modelTransformer($modelTransformerClass)
            ->setModel($model)
            ->setContext($context)
            ->toTransformed($mapping);
    }

    /**
     * @param string $modelTransformerClass
     * @return ModelTransformer
     */
    protected function modelTransformer($modelTransformerClass)
    {
        return new $modelTransformerClass;
    }

    /**
     * @param mixed $data
     * @param callable|null $callback
     * @return array|null
     */
    protected function modelSafe($data, callable $callback = null)
    {
        return Helper::default($data, null, $callback);
    }
}
