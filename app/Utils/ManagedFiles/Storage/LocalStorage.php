<?php

namespace App\Utils\ManagedFiles\Storage;

class LocalStorage extends HandledStorage implements ILocalStorage
{
    use LocalStorageTrait;

    const NAME = 'local';
}
