<?php

namespace App\Utils\Files\FileWriter;

class CsvWriter extends FileWriter
{
    protected $notWritten;

    public function __construct($name = null, $stored = false, $toDirectory = '', $isRelative = false)
    {
        if (is_array($name)) {
            $name['extension'] = 'csv';
        } else {
            $name = [
                'name' => $name,
                'extension' => 'csv',
            ];
        }

        parent::__construct($name, $stored, $toDirectory, $isRelative);

        $this->notWritten = true;
    }

    public function write($anything)
    {
        if ($this->notWritten) {
            fputs($this->handler, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            $this->notWritten = false;
        }
        fputcsv($this->handler, $anything);
        return $this;
    }

    public function writeMany($anything)
    {
        foreach ($anything as $item) {
            $this->write($item);
        }
        return $this;
    }
}
