<?php

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
