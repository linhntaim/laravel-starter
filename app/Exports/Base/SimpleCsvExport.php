<?php

namespace App\Exports\Base;

class SimpleCsvExport extends CsvExport
{
    public const NAME = 'simple';

    protected $headers;
    protected $data;
    protected $name;

    public function __construct(array $data, array $headers = [], $name = null)
    {
        parent::__construct();

        $this->headers = $headers;
        $this->data = $data;
        $this->name = $name ? $name : static::NAME;
    }

    public function getName()
    {
        return $this->name;
    }

    public function csvHeaders()
    {
        return $this->headers;
    }

    protected function csvExporting()
    {
        return $this->csvStore($this->data);
    }
}
