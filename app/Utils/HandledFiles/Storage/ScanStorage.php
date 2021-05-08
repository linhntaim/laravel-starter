<?php

namespace App\Utils\HandledFiles\Storage;

use App\Utils\ConfigHelper;
use App\Utils\HandledFiles\Storage\Scanners\Scanner;

class ScanStorage extends HandledStorage
{
    public const NAME = 'scan';

    protected $scanDiskName;

    /**
     * @var Scanner
     */
    protected $scanner;

    public function __construct($scanner = null)
    {
        $this->scanDiskName = ConfigHelper::get('handled_file.scan.disk');
        parent::__construct($this->scanDiskName);

        $this->scanner = $scanner ?: $this->getDefaultScanner();
    }

    public function getDiskName()
    {
        return $this->scanDiskName;
    }

    public function getDefaultScanner()
    {
        $scannerClass = ConfigHelper::get('handled_file.scan.scanner');
        return $scannerClass ? new $scannerClass : null;
    }

    public function scan()
    {
        if ($this->scanner) {
            return $this->scanner->scan($this);
        }
        return Scanner::SCAN_TRUE;
    }

    public function delete()
    {
        if ($this->scanner) {
            $this->scanner->delete($this);
        }
        return parent::delete();
    }
}
