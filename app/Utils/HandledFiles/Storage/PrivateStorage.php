<?php

namespace App\Utils\HandledFiles\Storage;

class PrivateStorage extends LocalStorage
{
    const NAME = 'private';

    public function getUrl()
    {
        return null;
    }
}
