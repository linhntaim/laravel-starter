<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Regex\RegexBasedStringGenerator;

class AndGeneratorNode extends GeneratorNode
{
    public function generate()
    {
        $value = '';
        foreach ($this->children as $child) {
            $value .= $child->generate();
        }
        return $value;
    }
}
