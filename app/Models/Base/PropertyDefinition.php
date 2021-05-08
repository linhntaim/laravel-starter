<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

use App\ModelCasts\CallbacksCast;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class PropertyDefinition
{
    /**
     * @var array
     */
    protected $definition;

    public function __construct(array $definition)
    {
        $this->definition = $definition;
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return array_keys($this->definition);
    }

    /**
     * @param string $name
     * @return CastsAttributes|null
     */
    public function getCaster(string $name)
    {
        $cast = $this->definition[$name]['cast'] ?? null;
        return is_array($cast) ? new CallbacksCast($cast) : $cast;
    }
}
