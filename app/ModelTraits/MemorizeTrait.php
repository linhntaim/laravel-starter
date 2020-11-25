<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

use Illuminate\Support\Str;

trait MemorizeTrait
{
    protected $memories = [];

    protected function memorized($key)
    {
        return isset($this->memories[$key]);
    }

    /**
     * @param $key
     * @param \Closure|callable|null $valueCallback
     * @param \Closure|callable|null $revalidateCallback
     * @return mixed|null
     */
    protected function remind($key, callable $valueCallback = null, callable $revalidateCallback = null)
    {
        if ($this->memorized($key) && (is_null($revalidateCallback) || $revalidateCallback($this->memories[$key]))) {
            return $this->memories[$key];
        }
        return $valueCallback ?
            $this->memorize($key, $valueCallback())
            : null;
    }

    protected function memorize($key, $value)
    {
        $this->memories[$key] = $value;
        return $value;
    }

    protected function unmemorized($key)
    {
        unset($this->memories[$key]);
        return $this;
    }

    protected function forgetAll()
    {
        $this->memories = [];
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'unmemorized')) {
            $key = Str::snake(Str::substr($name, 11));
            return $this->unmemorized($key);
        }
        if (Str::startsWith($name, 'memorize')) {
            $key = Str::snake(Str::substr($name, 11));
            return $this->memorize($key, ...$arguments);
        }

        return parent::__call($name, $arguments);
    }
}
