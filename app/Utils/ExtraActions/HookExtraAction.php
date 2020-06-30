<?php

namespace App\Utils\ExtraActions;

class HookExtraAction
{
    private $actions;

    public function __construct()
    {
        $this->actions = [];
    }

    public function existed($name)
    {
        return isset($this->actions[$name]);
    }

    public function add($name, $callback)
    {
        if (!$this->existed($name)) {
            $this->actions[$name] = [];
        }

        $this->actions[$name][] = $callback;
        return count($this->actions[$name]) - 1;
    }

    public function activate($name, ...$params)
    {
        $activated = [];
        if ($this->existed($name)) {
            foreach ($this->actions[$name] as $callback)
            $activated[] = $callback(...$params);
        }
        return $activated;
    }
}
