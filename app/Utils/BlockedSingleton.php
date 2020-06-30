<?php

namespace App\Utils;

trait BlockedSingleton
{
    protected static $blockedInstances = [];

    public static function getInstance($fresh = false, callable $callback = null)
    {
        if (empty(static::$blockedInstances) || $fresh) {
            $instance = new static();
            static::$blockedInstances[] = $callback ? $callback($instance) : $instance;
        }
        return end(static::$blockedInstances);
    }

    public static function getOut()
    {
        array_pop(static::$blockedInstances);
    }

    private function __construct()
    {
        $this->onCreating();
    }

    protected function onCreating()
    {

    }
}
