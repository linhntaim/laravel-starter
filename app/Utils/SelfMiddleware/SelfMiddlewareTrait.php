<?php

namespace App\Utils\SelfMiddleware;

trait SelfMiddlewareTrait
{
    /**
     * @return array
     */
    protected function getSelfMiddlewares()
    {
        return isset($this->selfMiddlewares) ? $this->selfMiddlewares : [];
    }

    protected function selfMiddleware()
    {
        foreach ($this->getSelfMiddlewares() as $selfMiddleware) {
            $this->createSelfMiddleware($selfMiddleware)->handle($this);
        }
    }

    /**
     * @param $selfMiddleware
     * @return ISelfMiddleware
     */
    protected function createSelfMiddleware($selfMiddleware)
    {
        return new $selfMiddleware;
    }
}
