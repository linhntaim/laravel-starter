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

    public function toArray($request)
    {
        $value = $this->value;
        return $this->mergeInWithCurrentArray($request, [
            [
                'value' => $value instanceof Model ? $this->modelTransform($value, $request) : $value,
            ],
        ]);
    }
}
