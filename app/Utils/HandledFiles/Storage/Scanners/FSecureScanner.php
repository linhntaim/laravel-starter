<?php

namespace App\Utils\HandledFiles\Storage\Scanners;

use App\Utils\HandledFiles\Storage\ScanStorage;

class FSecureScanner extends Scanner
{
    const SCANNED_EXTENSION = 'scanned';

    public function getRelativeScanPath(ScanStorage $storage)
    {
        return sprintf('%s.%s', $storage->getRelativePath(), static::SCANNED_EXTENSION);
    }

    public function scan(ScanStorage $storage)
    {
        $scanPath = $this->getRelativeScanPath($storage);
        if ($storage->exists($scanPath)) {
            $scanned = $storage->getContentRelativePath($scanPath);
            return preg_match('/1 files scanned/', $scanned) === 1
                && preg_match('/1 files infected/', $scanned) !== 1;
        }
        return false;
    }

    public function delete(ScanStorage $storage)
    {
        $storage->deleteRelativePath($this->getRelativeScanPath($storage));
    }
}