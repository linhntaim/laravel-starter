<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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

    public abstract function setContent($content);

    public abstract function getContent();
}
