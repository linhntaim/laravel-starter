<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources\Base;

use App\Http\Requests\Request;
use App\Utils\GuardArrayTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MergeValue;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Arr;

class ModelResource extends JsonResource implements IModelResource
{
    use ModelResourceTrait, GuardArrayTrait;

    public static $wrap = 'model';

    public $preserveKeys = true;

    protected $hidden = [];

    protected $guarded = [];

    protected $escaped = [];

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
    protected function guard(array $array)
    {
        return $this->guardEmptyValueOfAssocArray($array, $this->guarded);
    }

    protected function escape(array $array)
    {
        array_walk($array, function (&$item, $key) {
            if (is_string($key)) {
                if (!in_array($key, $this->escaped)
                    || $item instanceof MissingValue) return;
                if ($item instanceof MergeValue) {
                    $item->data = $this->escapeHtml($item->data);
                    return;
                }
                $item = $this->escapeHtml($item);
                return;
            }
            if ($item instanceof MergeValue) {
                $item->data = $this->escape($item->data);
                return;
            }
        });
        return $array;
    }

    protected function escapeHtml($value)
    {
        if (is_string($value)) {
            return escapeHtml($value);
        }
        if (is_array($value)) {
            return array_map(function ($item) {
                return $this->escapeHtml($item);
            }, $value);
        }
        return $value;
    }

    protected function toOriginalArray($request)
    {
        return parent::toArray($request);
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
    protected function toCustomArray($request)
    {
        return $this->toCurrentArray($request);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->guard(
            $this->escape(
                $this->toCustomArray($request)
            )
        );
    }
}