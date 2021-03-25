<?php

namespace App\Utils\HandledFiles\Storage;

use App\Utils\ConfigHelper;
use App\Utils\HandledFiles\Storage\Encrypters\Encrypter;

trait EncryptionStorageTrait
{
    protected $encrypted;

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
        $encrypterClass = ConfigHelper::get('handled_file.encryption.encrypter');
        return $encrypterClass ? new $encrypterClass : null;
    }

    public function encrypt()
    {
        if ($encrypter = $this->encrypter()) {
            $encrypter->encrypt($this);
        }
        return $this;
    }

    public function decrypt()
    {
        if ($encrypter = $this->encrypter()) {
            $encrypter->decrypt($this);
        }
        return $this;
    }

    public function streamDecrypt()
    {
        if ($encrypter = $this->encrypter()) {
            $encrypter->streamDecrypt($this);
        }
        return $this;
    }
}