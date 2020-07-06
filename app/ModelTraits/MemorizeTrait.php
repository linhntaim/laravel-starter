<?php

namespace App\ModelTraits;

use Illuminate\Support\Str;

trait MemorizeTrait
{
    protected $memories = [];

    protected function memorized($key)
    {
        return isset($this->memories[$key]);
    }

    protected function remind($key)
    {
        return isset($this->memories[$key]) ? $this->memories[$key] : null;
    }

    protected function memorize($key, $value)
    {
        $this->memories[$key] = $value;
        return $value;
    }

    protected function unmemorized($key)
    {
        unset($this->memories[$key]);
        return true;
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
