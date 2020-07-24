<?php

namespace App\Utils\HandledFiles\Storage;

abstract class Storage
{
    const NAME = '';

    public function getName()
    {
        return static::NAME;
    }

    /**
     * @param $data
     * @return Storage|IUrlStorage
     */
    public abstract function setData($data);

    public abstract function getData();
}
