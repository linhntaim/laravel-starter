<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PropertyResource
 * @package App\ModelResources\Base
 * @property string $name
 * @property mixed $value
 */
class PropertyResource extends ModelResource
{
    use ModelTransformTrait;

    protected function toCustomArray($request)
    {
        $value = $this->value;
        return [
            $this->merge($this->toCurrentArray($request)),
            $this->merge([
                'value' => $value instanceof Model ? $this->modelTransform($value, $request) : $value,
            ]),
        ];
    }
}
