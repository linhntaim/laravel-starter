<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources\Base;

use App\Http\Requests\Request;
use App\Utils\GuardArrayTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ModelResource extends JsonResource implements IModelResource
{
    use ModelResourceTrait, GuardArrayTrait;

    public static $wrap = 'model';

    public $preserveKeys = true;

    protected $hidden = [];

    protected $guarded = [];

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

    /**
     * @param array $array
     * @return array
     */
    public function guard(array $array)
    {
        return $this->guardEmptyValueOfAssocArray($array, $this->guarded);
    }

    protected function toOriginalArray($request)
    {
        if (is_null($this->resource)) {
            return [];
        }

        return is_array($this->resource)
            ? $this->resource
            : $this->resource->toArray();
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function toCurrentArray($request)
    {
        return Arr::except($this->toOriginalArray($request), $this->hidden);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->guard($this->toCustomArray($request));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toCustomArray($request)
    {
        return $this->toCurrentArray($request);
    }
}