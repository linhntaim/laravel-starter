<?php

namespace App\Utils\HandledFiles\Storage\Encrypters;

use App\Utils\HandledFiles\Storage\HandledStorage;

abstract class Encrypter
{
    const ENCRYPT_EXTENSION = 'encrypted';

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

    public abstract function encrypt(HandledStorage $storage);

    public abstract function decrypt(HandledStorage $storage);

    public abstract function streamDecrypt(HandledStorage $storage);
}