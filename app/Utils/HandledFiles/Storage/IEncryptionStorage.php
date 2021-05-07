<?php

namespace App\Utils\HandledFiles\Storage;

interface IEncryptionStorage
{
    public function setEncrypted($encrypted = true);

    public function encrypted();

    public function encrypt($copy = false);

    public function decrypt($copy = false);

    public function streamDecrypt();
}
