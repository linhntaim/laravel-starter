<?php

namespace App\Utils\HandledFiles\Storage\Scanners;

use App\Utils\HandledFiles\Storage\ScanStorage;

abstract class Scanner
{
    const SCANNING = 3;
    const SCAN_FALSE = 2;
    const SCAN_TRUE = 1;

    /**
     * @param ScanStorage $storage
     * @return int
     */
    public abstract function scan(ScanStorage $storage);

    /**
     * @param ScanStorage $storage
     */
    public abstract function delete(ScanStorage $storage);
}