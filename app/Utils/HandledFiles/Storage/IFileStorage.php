<?php

namespace App\Utils\HandledFiles\Storage;

interface IFileStorage
{
    public function getSize();

    public function getMime();

    public function getContent();
}