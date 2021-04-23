<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources\Base;

use App\Http\Requests\Request;
use App\Vendors\Illuminate\Support\Arr;
use App\Vendors\Illuminate\Support\HtmlString;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MergeValue;
use Illuminate\Http\Resources\MissingValue;

class ModelResource extends JsonResource implements IModelResource
{
    use ModelResourceTrait;

    public static $wrap = 'model';

    public $hidden = [];

    public $escaped = [];

    public $guarded = [];

    /**
     * @param mixed $resource
     * @return ModelResourceCollection
     */
    public static function collection($resource)
    {
        return tap(new ModelResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }

    public function resolve($request = null)
    {
        return $this->guard(
            $this->escape(
                $this->hide(
                    parent::resolve($request)
                )
            )
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->toCurrentArray($request);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected final function toCurrentArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * @param Request $request
     * @param array ...$data
     * @return array
     */
    protected function mergeAllWithCurrentArray($request, ...$data)
    {
        return $this->mergeInWithCurrentArray($request, $data);
    }

    /**
     * @param Request $request
     * @param array $data
     * @return array
     */
    protected function mergeInWithCurrentArray($request, array $data)
    {
        array_unshift($data, $this->toCurrentArray($request));
        return $this->mergeIn($data);
    }

    /**
     * @param array ...$data
     * @return array
     */
    protected function mergeAll(...$data)
    {
        return $this->mergeIn($data);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function mergeIn(array $data)
    {
        return array_map(function ($data) {
            return $data instanceof MergeValue || $data instanceof MissingValue ?
                $data : $this->merge($data);
        }, $data);
    }

    protected function hide($data)
    {
        if (empty($this->hidden)) return $data;
        return Arr::except($data, $this->hidden);
    }

    protected function escape($data)
    {
        if (empty($this->escaped)) return $data;
        foreach ($data as $key => &$value) {
            if (in_array($key, $this->escaped, true)) {
                $value = HtmlString::escapes($value);
            }
        }
        return $data;
    }

    protected function guard($data)
    {
        if (empty($this->guarded)) return $data;
        return Arr::jsonGuard($data, $this->guarded);
    }
}
