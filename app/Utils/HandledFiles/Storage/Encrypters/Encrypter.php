<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Storage\Encrypters;

use App\Utils\HandledFiles\Storage\HandledStorage;

abstract class Encrypter
{
    public const ENCRYPT_EXTENSION = 'encrypted';

    public function getExtension()
    {
        return '.' . static::ENCRYPT_EXTENSION;
    }

    public function encryptedRelativePath($relativePath)
    {
        return $relativePath . $this->getExtension();
    }

    public function decryptedRelativePath($encryptedRelativePath)
    {
        $extension = $this->getExtension();
        return mb_substr($encryptedRelativePath, 0, mb_strlen($encryptedRelativePath) - mb_strlen($extension));
    }

    public abstract function encrypt(HandledStorage $storage, $copy = false);

    public abstract function decrypt(HandledStorage $storage, $copy = false);

    public abstract function streamDecrypt(HandledStorage $storage);
}
