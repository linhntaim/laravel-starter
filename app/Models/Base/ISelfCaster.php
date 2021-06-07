<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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
     * @return bool
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
