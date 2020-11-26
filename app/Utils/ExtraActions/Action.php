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
        $id = $id ? $id : count($this->registeredCallbacks[$namespace]);
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
        $this->executed = [];
        foreach ($this->getCallbacksByNamespace($namespace) as $id => $callback) {
            $this->execute($id, $callback, $params);
            $callback(...$params);
        }
        return $this->result();
    }

    protected function execute($id, $callback, $params)
    {
        $executed = $callback(...$params);
        $this->executed[$id] = $executed;
        return $executed;
    }

    protected function result()
    {
        return $this->executed;
    }
}
