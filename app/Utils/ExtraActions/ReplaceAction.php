<?php

namespace App\Utils\ExtraActions;

class ReplaceAction extends Action
{
    /**
     * @var callable[]|bool[]
     */
    protected $conditionCallbacks;

    /**
     * @var callable[]
     */
    protected $defaultCallbacks;

    /**
     * @var array[]
     */
    protected $defaultParams;

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
        return is_bool($callback) ? $callback : ($callback ? $callback(...$params) : true);
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
            parent::activate($namespace, $params);
            return empty($this->executed) ?
                $this->executeDefault($namespace, $params) : $this->executed;
        }
        return $this->clearResult()->executeDefault($namespace, $params);
    }
}
