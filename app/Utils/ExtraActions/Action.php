<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ExtraActions;

abstract class Action
{
    protected $registeredCallbacks = [];

    protected $executed = [];

    /**
     * @param callable $callback
     * @param string $namespace
     * @param string|null $id
     * @return int|mixed
     */
    public function register(callable $callback, string $namespace, $id = null)
    {
        if (!isset($this->registeredCallbacks[$namespace])) {
            $this->registeredCallbacks[$namespace] = [];
        }
        $id = $id ?: count($this->registeredCallbacks[$namespace]);
        $this->registeredCallbacks[$namespace][$id] = $callback;
        return $id;
    }

    /**
     * @param string $namespace
     * @return callable[]
     */
    protected function getCallbacksByNamespace($namespace)
    {
        return isset($this->registeredCallbacks[$namespace]) ?
            $this->registeredCallbacks[$namespace] : [];
    }

    public function activate(string $namespace, ...$params)
    {
        $this->clearResult($namespace);
        foreach ($this->getCallbacksByNamespace($namespace) as $id => $callback) {
            $this->execute($namespace, $id, $callback, $params);
        }
        return $this->result($namespace);
    }

    protected function execute(string $namespace, $id, $callback, $params)
    {
        $executed = $callback(...$params);
        $this->executed[$namespace][$id] = $executed;
        return $executed;
    }

    protected function result(string $namespace)
    {
        return $this->executed[$namespace];
    }

    protected function clearResult(string $namespace)
    {
        $this->executed[$namespace] = [];
        return $this;
    }
}
