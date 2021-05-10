<?php

namespace App\ModelCasts;

use App\Models\Base\ISafeArrayCast;
use Illuminate\Database\Eloquent\Model;

class SafeArrayCast
{
    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        return jsonDecodeArray($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param string $key
     * @param array $value
     * @param array $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        if (!($model instanceof ISafeArrayCast) || $model->isSafeArrayCastEnabled($key)) {
            $value = array_merge($model->{$key}, $value);
        }
        return json_encode($value);
    }
}