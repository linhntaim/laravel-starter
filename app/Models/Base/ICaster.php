<?php

namespace App\Models\Base;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

interface ICaster
{
    /**
     * @param string $key
     * @param array $attributes
     * @return CastsAttributes
     */
    public function getCaster(string $key, array $attributes);
}
