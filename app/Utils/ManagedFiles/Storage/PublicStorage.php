<?php

namespace App\Utils\ManagedFiles\Storage;

class PublicStorage extends HandledStorage implements ILocalStorage
{
    use LocalStorageTrait;

    const NAME = 'public';
}
