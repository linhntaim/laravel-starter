<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ExtraActions;

class FilterAction extends Action
{
    protected $contents = [];

    public function activate(string $namespace, ...$params)
    {
        $this->contents[$namespace] = $params[0];
        return parent::activate($namespace, $params);
    }

    protected function execute(string $namespace, $id, $callback, $params)
    {
        $this->contents[$namespace] = parent::execute(
            $namespace,
            $id,
            $callback,
            [$this->contents[$namespace]]
        );
        return $this->contents[$namespace];
    }

    protected function result(string $namespace)
    {
        return $this->contents[$namespace];
    }
}
