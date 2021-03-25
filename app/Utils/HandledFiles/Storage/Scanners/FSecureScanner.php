<?php

namespace App\Utils\HandledFiles\Storage\Scanners;

use App\Utils\HandledFiles\Storage\ScanStorage;

class FSecureScanner extends Scanner
{
    const SCANNED_EXTENSION = 'scanned';
    const MALWARE_EXTENSION = 'malware';

    public function getRelativeScanPath(ScanStorage $storage)
    {
        return sprintf('%s.%s', $storage->getRelativePath(), static::SCANNED_EXTENSION);
    }

    public function getRelativeMalwarePath(ScanStorage $storage)
    {
        return sprintf('%s.%s', $storage->getRelativePath(), static::MALWARE_EXTENSION);
    }

    public function scan(ScanStorage $storage)
    {
        $scanPath = $this->getRelativeScanPath($storage);
        if ($storage->exists($scanPath)) {
            $scanned = $storage->getContentRelativePath($scanPath);
            return preg_match('/1 files scanned/', $scanned) === 1 && preg_match('/1 files infected/', $scanned) !== 1 ?
                Scanner::SCAN_TRUE : Scanner::SCAN_FALSE;
        }
        return Scanner::SCANNING;
    }

    public function delete(ScanStorage $storage)
    {
        $storage->deleteRelativePath($this->getRelativeScanPath($storage));
        if ($storage->exists($malwareRelativePath = $this->getRelativeMalwarePath($storage))) {
            $storage->deleteRelativePath($malwareRelativePath);
        }
    }
}