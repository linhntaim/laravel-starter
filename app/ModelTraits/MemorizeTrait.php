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

    protected function unmemorized($key)
    {
        unset($this->memories[$key]);
        return $this;
    }

    protected function unmemorizedAll()
    {
        $this->memories = [];
        return $this;
    }

    protected function __memorizeCall($name, $arguments)
    {
        if (Str::startsWith($name, 'unmemorized')) {
            $key = Str::snake(Str::after($name, 'unmemorized'));
            return $this->unmemorized($key);
        }
        if (Str::startsWith($name, 'memorize')) {
            $key = Str::snake(Str::after($name, 'memorize'));
            return $this->memorize($key, ...$arguments);
        }
        return false;
    }

    public function __call($name, $arguments)
    {
        if ($this->__memorizeCall($name, $arguments) !== false) {
            return $this;
        }

        return parent::__call($name, $arguments);
    }
}
