<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Storage;

interface IFileStorage
{
    public function getSize();

    public function getMime();

    public function getContent();
}