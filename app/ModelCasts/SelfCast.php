<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelCasts;

use App\Models\Base\ICaster;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SelfCast implements CastsAttributes
{
    public function set($model, string $key, $value, array $attributes)
    {
        if ($model instanceof ICaster) {
            return $model->getCaster($key, $attributes)
                ->set($model, $key, $value, $attributes);
        }
        return $value;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        if ($model instanceof ICaster) {
            return $model->getCaster($key, $attributes)
                ->get($model, $key, $value, $attributes);
        }
        return $value;
    }
}
