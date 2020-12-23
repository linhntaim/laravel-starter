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
        return isset($this->callbacks['set']) ?
            $this->callbacks['set']
            : (isset($this->callbacks[0]) ? $this->callbacks[0] : null);
    }

    /**
     * @return callable|null
     */
    public function getGetCallback()
    {
        return isset($this->callbacks['get']) ?
            $this->callbacks['get']
            : (isset($this->callbacks[1]) ? $this->callbacks[1] : null);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($setCallback = $this->getSetCallback()) {
            return $setCallback($value);
        }
        return null;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        if ($getCallback = $this->getGetCallback()) {
            return $getCallback($value);
        }
        return null;
    }
}
