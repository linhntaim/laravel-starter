<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelCasts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CallbacksCast implements CastsAttributes
{
    /**
     * @var callable[]|array
     */
    protected $callbacks;

    /**
     * CallbacksAttributeCast constructor.
     * @param array $callbacks
     */
    public function __construct(array $callbacks)
    {
        $this->callbacks = $callbacks;
    }

    /**
     * @return callable|null
     */
    public function getSetCallback()
    {
        return $this->callbacks['set'] ?? ($this->callbacks[0] ?? null);
    }

    /**
     * @return callable|null
     */
    public function getGetCallback()
    {
        return $this->callbacks['get'] ?? ($this->callbacks[1] ?? null);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($setCallback = $this->getSetCallback()) {
            return $setCallback($model, $key, $value, $attributes);
        }
        return null;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        if ($getCallback = $this->getGetCallback()) {
            return $getCallback($model, $key, $value, $attributes);
        }
        return null;
    }
}
