<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Storage;

interface IResponseStorage
{
    public function responseFile($mime, $headers = []);

    public function responseDownload($name, $mime, $headers = []);
}
