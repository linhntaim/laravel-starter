<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

use Illuminate\Support\Str;

trait ArrayValuedAttributesTrait
{
    public function getAttribute($key)
    {
        if ($this->ifGetArrayValueAttribute($key, $value)) {
            return $value;
        }

        return parent::getAttribute($key);
    }

    protected function ifGetArrayValueAttribute($key, &$value)
    {
        if (Str::endsWith($key, '_array_value')) {
            $key = Str::before($key, '_array_value');
            if (array_key_exists($key, $this->attributes)) {
                if (empty($this->attributes[$key])) {
                    return [];
                }
                $value = json_decode($this->attributes[$key], true);
                if (!is_array($value)) {
                    $value = [];
                }
                return true;
            }
        }
        return false;
    }

    public function setAttribute($key, $value)
    {
        if ($this->ifSetArrayValueAttribute($key, $value)) {
            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    protected function ifSetArrayValueAttribute($key, $value)
    {
        if (!is_array($value)) {
            $value = [];
        }
        if (Str::endsWith($key, '_overridden_array_value')) {
            $key = Str::before($key, '_overridden_array_value');
            $this->attributes[$key] = json_encode($value);
            return true;
        } elseif (Str::endsWith($key, '_array_value')) {
            $storedValue = $this->getAttribute($key);
            if (!empty($value)) {
                foreach ($value as $name => $data) {
                    $storedValue[$name] = $data;
                }
            }

            $key = Str::before($key, '_array_value');
            $this->attributes[$key] = json_encode($storedValue);
            return true;
        }
        return false;
    }
}
