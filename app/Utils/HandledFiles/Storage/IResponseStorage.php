<?php

namespace App\Utils\HandledFiles\Storage;

interface IResponseStorage
{
    public function responseFile($mime, $headers = []);

    public function responseDownload($name, $mime, $headers = []);
}
