<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Regex\RegexBasedStringGenerator;

class LeafGeneratorNode extends GeneratorNode
{
    protected $value;

    public function __construct($value)
    {
        parent::__construct();

        $this->value = $value;
    }

    public function generate()
    {
        return $this->value;
    }
}
