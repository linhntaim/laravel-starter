<?php

namespace App\Utils\HandledFiles\Storage;

interface IEncryptionStorage
{
    public function setEncrypted($encrypted = true);

    public function encrypted();

    public function encrypt();

    public function decrypt();

    public function streamDecrypt();
}