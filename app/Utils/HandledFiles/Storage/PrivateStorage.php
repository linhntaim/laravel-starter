<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Storage;

class PrivateStorage extends LocalStorage
{
    const NAME = 'private';

    public function getUrl()
    {
        return null;
    }
}
