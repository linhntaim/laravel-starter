<?php

namespace App\Imports;

use App\Utils\ClassTrait;
use App\Utils\ValidationTrait;

abstract class Import
{
    use ClassTrait, ValidationTrait;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public abstract function import();
}
