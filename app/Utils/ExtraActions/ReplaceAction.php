<?php

namespace App\Utils\ExtraActions;

class ReplaceAction extends Action
{
    /**
     * @var callable[]|bool[]
     */
    protected $conditionCallbacks = [];

    /**
     * @var callable[]
     */
    protected $defaultCallbacks = [];

    /**
     * @param string $namespace
     * @param callable|bool|null $conditionCallback
     * @return ReplaceAction
     */
    public function setConditionCallback(string $namespace, $conditionCallback = true)
    {
        $this->conditionCallbacks[$namespace] = $conditionCallback;
        return $this;
    }

    /**
     * @param string $namespace
     * @param callable|null $defaultCallback
     * @return ReplaceAction
     */
    public function setDefaultCallback(string $namespace, callable $defaultCallback = null)
    {
        $this->defaultCallbacks[$namespace] = $defaultCallback;
        return $this;
    }

    /**
     * @param string $namespace
     * @param array $params
     * @return bool
     */
    protected function executeCondition(string $namespace, array $params)
    {
        $callback = isset($this->conditionCallbacks[$namespace]) ? $this->conditionCallbacks[$namespace] : true;
        if (is_bool($callback)) return $callback;
        return $callback ? $callback(...$params) : true;
    }

    /**
     * @param string $namespace
     * @param array $params
     * @return mixed|null
     */
    protected function executeDefault(string $namespace, array $params)
    {
        $callback = isset($this->defaultCallbacks[$namespace]) ? $this->defaultCallbacks[$namespace] : null;
        return $callback ? $callback(...$params) : null;
    }

    public function activate(string $namespace, ...$params)
    {
        if ($this->executeCondition($namespace, $params)) {
            $executed = parent::activate($namespace, $params);
            return empty($executed) ? $this->executeDefault($namespace, $params) : $executed;
        }
        return $this->clearResult($namespace)
            ->executeDefault($namespace, $params);
    }
}
