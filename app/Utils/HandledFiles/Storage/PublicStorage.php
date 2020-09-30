<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Storage;

class PublicStorage extends LocalStorage implements IUrlStorage
{
    const NAME = 'public';
}
