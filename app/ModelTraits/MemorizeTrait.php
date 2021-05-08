<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

trait MemorizeTrait
{
    protected $memories = [];

    protected function memorized($key)
    {
        return isset($this->memories[$key]);
    }

    /**
     * @param $key
     * @param callable|null $valueCallback
     * @param callable|null $revalidateCallback
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

    public function unmemorized($key)
    {
        unset($this->memories[$key]);
        return $this;
    }

    public function unmemorizedAll()
    {
        $this->memories = [];
        return $this;
    }
}
