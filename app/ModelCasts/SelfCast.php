<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelCasts;

use App\Models\Base\ISelfCaster;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SelfCast implements CastsAttributes
{
    public function set($model, string $key, $value, array $attributes)
    {
        if ($model instanceof ISelfCaster) {
            $model->applyCasters();
            if (($caster = $model->getCaster($key)) && $caster instanceof CastsAttributes) {
                return $caster->set($model, $key, $value, $attributes);
            }
        }
        return $value;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        if ($model instanceof ISelfCaster) {
            $model->applyCasters();
            if (($caster = $model->getCaster($key)) && $caster instanceof CastsAttributes) {
                return $caster->get($model, $key, $value, $attributes);
            }
        }
        return $value;
    }
}
