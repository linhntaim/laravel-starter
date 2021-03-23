<?php

namespace App\Utils\HandledFiles\Storage;

use App\Utils\HandledFiles\Storage\Scanners\Scanner;

class ScanStorage extends HandledStorage
{
    const NAME = 'scan';

    /**
     * @var Scanner
     */
    protected $scanner;

    public function __construct($scanner = null)
    {
        parent::__construct(config('filesystems.scan'));

        $this->scanner = $scanner ? $scanner : $this->getDefaultScanner();
    }

    public function getDefaultScanner()
    {
        $scannerClass = config('filesystems.scanner');
        return $scannerClass ? new $scannerClass : null;
    }

    public function scan()
    {
        if ($this->scanner) {
            return $this->scanner->scan($this);
        }
        return false;
    }

    public function delete()
    {
        if ($this->scanner) {
            $this->scanner->delete($this);
        }
        return parent::delete();
    }
}