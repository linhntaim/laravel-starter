<?php

namespace App\Models\Base;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

interface ISelfCaster
{
    /**
     * @param string $key
     * @return CastsAttributes|string
     */
    public function getCaster(string $key);

    /**
     * @param string $key
     * @return boolean
     */
    public function hasCaster(string $key);

    /**
     * @param string $key
     * @param CastsAttributes|string $caster
     * @return static
     */
    public function setCaster(string $key, $caster);

    /**
     * @return static
     */
    public function applyCasters();
}
