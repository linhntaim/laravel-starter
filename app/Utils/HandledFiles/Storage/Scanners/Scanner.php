<?php

namespace App\Utils\HandledFiles\Storage\Scanners;

use App\Utils\HandledFiles\Storage\ScanStorage;

abstract class Scanner
{
    /**
     * @param ScanStorage $storage
     * @return boolean
     */
    public abstract function scan(ScanStorage $storage);

    /**
     * @param ScanStorage $storage
     */
    public abstract function delete(ScanStorage $storage);
}