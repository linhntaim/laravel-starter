<?php

namespace App\ModelTraits;

use Illuminate\Support\Str;

trait ArrayValuedAttributesTrait
{
    public function getAttribute($key)
    {
        if (!$key) {
            return [];
        }
        if (Str::endsWith($key, '_array_value')) {
            $key = Str::before($key, '_array_value');
            if (array_key_exists($key, $this->attributes)) {
                if (empty($this->attributes[$key])) {
                    return [];
                }
                $value = json_decode($this->attributes[$key], true);
                return $value === false ? [] : $value;
            }
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        if (!$key) {
            return null;
        }
        if (Str::endsWith($key, '_array_value')) {
            $storedValue = $this->getAttribute($key);
            if (!empty($value)) {
                foreach ($value as $name => $data) {
                    $storedValue[$name] = $data;
                }
            }

            $key = Str::before($key, '_array_value');
            $this->attributes[$key] = json_encode(empty($storedValue) ? [] : $storedValue);
            return $this;
        } elseif (Str::endsWith($key, '_overridden_array_value')) {
            $key = Str::before($key, '_overridden_array_value');
            $this->attributes[$key] = json_encode(empty($value) ? [] : $value);
            return $this;
        }

        return parent::setAttribute($key, $value);
    }
}
