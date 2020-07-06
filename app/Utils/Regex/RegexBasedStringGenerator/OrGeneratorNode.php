<?php

namespace App\Utils\Regex\RegexBasedStringGenerator;

class OrGeneratorNode extends GeneratorNode
{
    /**
     * @var int
     */
    protected $valuedNodeIndex;

    public function __construct()
    {
        parent::__construct();

        $this->valuedNodeIndex = -1;
    }

    public function valuedAtNone()
    {
        return $this->valuedNodeIndex < 0 || $this->valuedNodeIndex >= $this->childrenLength;
    }

    public function valuedAtLast()
    {
        return $this->valuedNodeIndex == $this->childrenLength - 1;
    }

    public function resetValuedNodeIndex()
    {
        $this->valuedNodeIndex = 0;
    }

    public function pickNextValuedNodeIndex()
    {
        ++$this->valuedNodeIndex;
    }

    public function pickRandomValuedNodeIndex()
    {
        $this->valuedNodeIndex = rand(0, ($this->childrenLength-1));
    }

    /**
     * @return GeneratorNode
     */
    public function getValuedNode()
    {
        return $this->children[$this->valuedNodeIndex];
    }

    public function getValuedNodeIndex()
    {
        return $this->valuedNodeIndex;
    }

    public function generate()
    {
        return $this->getValuedNode()->generate();
    }
}
