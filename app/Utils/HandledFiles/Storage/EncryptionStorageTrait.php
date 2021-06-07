<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Storage;

use App\Utils\ConfigHelper;
use App\Utils\HandledFiles\Storage\Encrypters\Encrypter;

trait EncryptionStorageTrait
{
    protected $encrypted = false;

    public function setEncrypted($encrypted = true)
    {
        $this->encrypted = $encrypted;
        return $this;
    }

    public function encrypted()
    {
        return $this->encrypted;
    }

    /**
     * @return Encrypter|null
     */
    public function encrypter()
    {
        return ($encryptorClass = ConfigHelper::get('handled_file.encryption.encrypter')) ?
            new $encryptorClass : null;
    }

    public function encrypt($copy = false)
    {
        if (($encryptor = $this->encrypter()) && !$this->encrypted()) {
            $encryptor->encrypt($this, $copy);
            $this->setEncrypted();
        }
        return $this;
    }

    public function decrypt($copy = false)
    {
        if (($encryptor = $this->encrypter()) && $this->encrypted()) {
            $encryptor->decrypt($this, $copy);
            $this->setEncrypted(false);
        }
        return $this;
    }

    public function streamDecrypt()
    {
        if (($encrypter = $this->encrypter()) && $this->encrypted()) {
            $encrypter->streamDecrypt($this);
        }
        return $this;
    }
}
