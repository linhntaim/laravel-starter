<?php

namespace App\Utils\ManagedFiles\Storage;

abstract class Storage
{
    const NAME = '';

    public function getName()
    {
        return static::NAME;
    }

    public abstract function getData();
}
