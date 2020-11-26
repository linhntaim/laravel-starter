<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ExtraActions;

class FilterAction extends Action
{
    protected $content;

    protected function execute($id, $callback, $params)
    {
        $this->content = parent::execute(
            $id,
            $callback,
            empty($this->executed) ? [$params[0]] : [$this->content]
        );
        return $this->content;
    }

    protected function result()
    {
        return $this->content;
    }
}
