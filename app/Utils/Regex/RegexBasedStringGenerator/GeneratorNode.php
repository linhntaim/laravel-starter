<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Regex\RegexBasedStringGenerator;

use App\Vendors\Illuminate\Support\Str;

abstract class GeneratorNode
{
    protected static $count = 0;

    protected $id;

    /**
     * @var array
     */
    protected $children;

    /**
     * @var int
     */
    protected $childrenLength;

    /**
     * @var GeneratorNode
     */
    protected $parent;

    public function __construct()
    {
        ++static::$count;

        $this->id = static::$count;
        $this->children = [];
        $this->childrenLength = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getChildrenLength()
    {
        return $this->childrenLength;
    }

    /**
     * @param GeneratorNode $node
     * @return static
     */
    public function setParent($node)
    {
        $this->parent = $node;
        return $this;
    }

    /**
     * @param GeneratorNode|string $node
     * @return static
     */
    public function addChild($node)
    {
        $this->children[] = $node instanceof GeneratorNode ?
            $node->setParent($this) : (new LeafGeneratorNode($node))->setParent($this);
        ++$this->childrenLength;

        return $this;
    }

    /**
     * @param array $nodes
     * @return static
     */
    public function addChildren(array $nodes)
    {
        foreach ($nodes as $node) {
            $this->addChild($node);
        }

        return $this;
    }

    public function getValuesOfLeafChildren()
    {
        $values = [];
        foreach ($this->children as $child) {
            if ($child instanceof LeafGeneratorNode) {
                $values[] = $child->generate();
            }
        }
        return $values;
    }

    /**
     * @param GeneratorNode $root
     * @return bool
     */
    public function beforeRandomGenerating($root)
    {
        if ($this instanceof OrGeneratorNode) {
            $this->pickRandomValuedNodeIndex();
            $this->getValuedNode()->beforeRandomGenerating($root);
        }
        elseif ($this instanceof AndGeneratorNode) {
            foreach ($this->children as $child) {
                $child->beforeRandomGenerating($root);
            }
        }
    }

    /**
     * @param GeneratorNode $root
     * @return bool
     */
    public function beforeGenerating($root)
    {
        if ($this instanceof OrGeneratorNode) {
            if ($this->valuedAtNone()) {
                $this->resetValuedNodeIndex();
                foreach ($this->children as $child) {
                    $child->beforeGenerating($root);
                }
                return true;
            }
            elseif (!$this->valuedAtLast()) {
                $valuedNode = $this->getValuedNode();

                if ($valuedNode instanceof LeafGeneratorNode || $valuedNode->beforeGenerating($root)) {
                    if ($root->canPickNext($this) === 0) {
                        return true;
                    }
                    $this->pickNextValuedNodeIndex();
                    $root->resetAfterNextPicked($this);
                }
                return false;
            }
            else { // valued at last
                foreach ($this->children as $child) {
                    if (!$child->beforeGenerating($root)) {
                        return false;
                    }
                }
                return true;
            }
        }
        elseif ($this instanceof AndGeneratorNode) {
            foreach ($this->children as $child) {
                if (!$child->beforeGenerating($root)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param OrGeneratorNode $currentOrNode
     * @return bool
     */
    public function canPickNext($currentOrNode)
    {
        if ($this->getId() == $currentOrNode->getId()) {
            return 2;
        }
        if ($this instanceof OrGeneratorNode) {
            if (!$this->valuedAtLast()) {
                if ($this->getValuedNode() instanceof LeafGeneratorNode) {
                    return 0;
                }
                foreach ($this->children as $child) {
                    switch ($child->canPickNext($currentOrNode)) {
                        case 2:
                            return 2;
                        case 0:
                            return 0;
                        default:
                            break;
                    }
                }
            }
            else {
                foreach ($this->children as $child) {
                    switch ($child->canPickNext($currentOrNode)) {
                        case 2:
                            return 2;
                        case 0:
                            return 0;
                        default:
                            break;
                    }
                }
            }
        }
        elseif ($this instanceof AndGeneratorNode) {
            for ($i = $this->childrenLength - 1; $i >= 0; --$i) {
                switch ($this->children[$i]->canPickNext($currentOrNode)) {
                    case 2:
                        return 2;
                    case 0:
                        return 0;
                    default:
                        break;
                }
            }
        }

        return 1;
    }

    public function resetAfterNextPicked($currentOrNode)
    {
        if ($this->getId() == $currentOrNode->getId()) {
            return false;
        }
        for ($i = $this->childrenLength - 1; $i >= 0; --$i) {
            if (!$this->children[$i]->resetAfterNextPicked($currentOrNode)) {
                return false;
            }
        }
        if ($this instanceof OrGeneratorNode) {
            $this->resetValuedNodeIndex();
        }
        return true;
    }

    public abstract function generate();

    public function draw($level = 0)
    {
        if ($this instanceof LeafGeneratorNode) {
            echo $this->drawId() . str_repeat('--', $level) . $this->generate() . PHP_EOL;
            return;
        }
        echo $this->drawId() . str_repeat('--', $level) . ($this instanceof AndGeneratorNode ? 'AND' : 'OR') . PHP_EOL;
        foreach ($this->children as $child) {
            $child->draw($level + 1);
        }
    }

    public function drawId()
    {
        return '#' . Str::fillFollow($this->id, static::$count, '0');
    }

    public function getFactor()
    {
        if ($this instanceof OrGeneratorNode) {
            $factor = 0;
            foreach ($this->children as $child) {
                $factor += $child->getFactor();
            }
            return $factor;
        }
        if ($this instanceof AndGeneratorNode) {
            $factor = 1;
            foreach ($this->children as $child) {
                $factor *= $child->getFactor();
            }
            return $factor;
        }
        return 1;
    }
}
