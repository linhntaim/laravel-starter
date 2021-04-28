<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources\Base;

use Illuminate\Container\Container;

trait ModelResourceTrait
{
    protected $wrapEnabled = false;

    public function enableWrapping($value = true)
    {
        $this->wrapEnabled = $value;
        return $this;
    }

    protected function getWrapped(callable $callback)
    {
        $original = static::$wrap;
        if (!$this->wrapEnabled) {
            static::$wrap = null;
        }
        $called = $callback();
        if (!$this->wrapEnabled) {
            static::$wrap = $original;
            $this->wrapEnabled = true;
        }
        return $called;
    }

    protected function getModel($request)
    {
        return $this->getWrapped(function () use ($request) {
            return (new ModelResourceResponse($this))->toModel($request);
        });
    }

    public function toModel($request)
    {
        return $this->getModel($request);
    }

    public function model($request = null)
    {
        return $this->toModel(
            $request ?: Container::getInstance()->make('request')
        );
    }
}
