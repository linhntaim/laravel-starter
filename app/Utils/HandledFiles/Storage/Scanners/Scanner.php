<?php

namespace App\Utils\HandledFiles\Storage\Scanners;

use App\Utils\HandledFiles\Storage\ScanStorage;

abstract class Scanner
{
    public const SCANNING = 3;
    public const SCAN_FALSE = 2;
    public const SCAN_TRUE = 1;

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
