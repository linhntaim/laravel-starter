<?php

namespace App\Utils\HandledFiles\Storage;

class PrivateStorage extends LocalStorage
{
    const NAME = 'local';

    public function getUrl()
    {
        return null;
    }
}
