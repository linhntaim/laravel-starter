<?php

namespace App\Utils\HandledFiles\Storage\Encrypters;

use App\Utils\HandledFiles\Storage\HandledStorage;
use SoareCostin\FileVault\Facades\FileVault;

class FileVaultEncrypter extends Encrypter
{
    public function encrypt(HandledStorage $storage)
    {
        $relativePath = $storage->getRelativePath();
        $encryptedRelativePath = $this->encryptedRelativePath($relativePath);
        FileVault::disk($storage->getDiskName())
            ->encrypt($relativePath, $encryptedRelativePath);
        $storage->setRelativePath($encryptedRelativePath);
    }

    public function decrypt(HandledStorage $storage)
    {
        $encryptedRelativePath = $storage->getRelativePath();
        $decryptedRelativePath = $this->decryptedRelativePath($encryptedRelativePath);
        FileVault::disk($storage->getDiskName())
            ->decrypt($encryptedRelativePath, $decryptedRelativePath);
        $storage->setRelativePath($decryptedRelativePath);
    }

    public function streamDecrypt(HandledStorage $storage)
    {
        FileVault::disk($storage->getDiskName())
            ->streamDecrypt($storage->getRelativePath());
    }
}