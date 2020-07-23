<?php

namespace App\Utils\HandledFiles\Storage;

abstract class Storage
{
    const NAME = '';

    public function getName()
    {
        return static::NAME;
    }

    public abstract function getData();
}
