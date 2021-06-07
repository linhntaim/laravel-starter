<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Regex\RegexBasedStringGenerator;

use App\Exceptions\AppException;
use App\Utils\ClassTrait;
use App\Utils\Regex\RegexParser;
use Hoa\Compiler\Llk\TreeNode;
use Hoa\Ustring\Ustring;

class Generator
{
    use ClassTrait;

    public const DEFAULT_LIMIT_QUANTIFICATION = 8;

    protected $hasQuantification;

    protected $hasFixedQuantification;

    protected $hasMoreQuantification;

    protected $lockedQuantification;

    protected $hasLimitCharacters;

    protected $limitCharacters;

    protected $limitQuantification;

    protected $tree;

    /**
     * Generator constructor.
     * abc(d|[0-9f-j])mno(x|y){1,3}
     * abc(d|[0-9f-j])+
     *
     * @param string $regex
     * @param callable|bool|null $parsedCallback
     * @param bool $debug
     * @param int $matchedLength
     * @throws
     */
    public function __construct($regex, $parsedCallback = false, $debug = false, $matchedLength = -1)
    {
        $this->hasQuantification = false;
        $this->hasFixedQuantification = false;
        $this->hasMoreQuantification = false;
        $this->lockedQuantification = false;

        $this->hasLimitCharacters = false;
        $this->limitQuantification = static::DEFAULT_LIMIT_QUANTIFICATION;

        $this->checkRegex(RegexParser::getInstance()->parse($regex, $parsedCallback));
        if ($this->hasFixedQuantification) {
            $this->lockedQuantification = true;
        }
        if ($matchedLength > 0) {
            $this->limitQuantification = $matchedLength;
            if (!$this->hasQuantification) {
                $regex = sprintf('(%s){%s}', $regex, $matchedLength);
            }
        }
        $this->tree = $this->buildGenerationTree(RegexParser::getInstance()->parse($regex, $parsedCallback));
        if ($debug) {
            $this->tree->draw();
        }
    }

    /**
     * Maximum quantity can be generated
     *
     * @return int
     */
    public function getFactor()
    {
        return $this->tree->getFactor();
    }

    /**
     * @param int $limitQuantity
     * @param int $limitCharacters
     * @param callable|null $generatedCallback
     * @param bool $isRandom
     * @return array
     */
    public function generate($limitQuantity = -1, $limitCharacters = -1, $generatedCallback = null, $isRandom = false)
    {
        if ($limitQuantity == 0 || $limitCharacters == 0) {
            return [];
        }

        $this->limitCharacters = $limitCharacters;
        if ($limitCharacters > 0) {
            $this->hasLimitCharacters = !$this->lockedQuantification; // must not locked
            $this->limitQuantification = $limitCharacters;
        }
        if ($limitQuantity > 0) {
            return $this->generateLimit($limitQuantity, $generatedCallback, $isRandom);
        }
        return $this->generateAll($generatedCallback);
    }

    /**
     * @param callable|null $generatedCallback
     * @return array
     */
    private function generateAll($generatedCallback = null)
    {
        $outputs = [];
        $prevOutput = null;
        while (true) {
            $this->tree->beforeGenerating($this->tree);
            $output = $this->tree->generate();
            if ($output === $prevOutput) {
                break; // cannot generate more unique outputs
            }
            $prevOutput = $output;
            if (!$this->hasLimitCharacters || strlen($output) <= $this->limitCharacters) {
                if (!empty($generatedCallback)) {
                    $generatedCallback($output);
                }
                else {
                    $outputs[] = $output;
                }
            }
        }
        return $outputs;
    }

    /**
     * @param int $limitQuantity
     * @param callable|null $generatedCallback
     * @param bool $isRandom
     * @return array
     */
    private function generateLimit($limitQuantity, $generatedCallback = null, $isRandom = false)
    {
        $outputs = [];
        $prevOutput = null;
        while (--$limitQuantity >= 0) {
            if ($isRandom) {
                $this->tree->beforeRandomGenerating($this->tree);
            }
            else {
                $this->tree->beforeGenerating($this->tree);
            }
            $output = $this->tree->generate();
            if ($output === $prevOutput) {
                break; // cannot generate more unique outputs
            }
            $prevOutput = $output;
            if ($this->hasLimitCharacters && strlen($output) > $this->limitCharacters) {
                ++$limitQuantity;
            }
            else {
                if (!empty($generatedCallback)) {
                    $generatedCallback($output);
                }
                else {
                    $outputs[] = $output;
                }
            }
        }
        return $outputs;
    }

    private function checkRegex(TreeNode $node)
    {
        switch ($node->getId()) {
            case '#expression':
            case '#capturing':
            case '#noncapturing':
            case '#namedcapturing':
                $this->checkRegex($node->getChild(0));
                return;
            case '#alternation':
            case '#class':
            case '#concatenation':
            case '#negativeclass':
                foreach ($node->getChildren() as $child) {
                    $this->checkRegex($child);
                }
                return;
            case '#quantification':
                $this->hasQuantification = true;
                switch ($node->getChild(1)->getValueToken()) {
                    case 'zero_or_one':
                    case 'exactly_n':
                    case 'n_to_m':
                        $this->hasFixedQuantification = true;
                        break;
                    case 'zero_or_more':
                    case 'one_or_more':
                    case 'n_or_more':
                        $this->hasMoreQuantification = true;
                        break;
                }
                $this->checkRegex($node->getChild(0));
                return;
            case '#range':
                $this->checkRegex($node->getChild(0));
                $this->checkRegex($node->getChild(1));
                return;
            case 'token':
            case '#internal_options':
                return;
            default:
                throw new AppException($this->__transErrorWithModule('token_not_supported'));
        }
    }

    /**
     * @param TreeNode $node
     * @return GeneratorNode|string
     * @throws
     */
    private function buildGenerationTree(TreeNode $node)
    {
        switch ($node->getId()) {
            case '#expression':
            case '#capturing':
            case '#noncapturing':
            case '#namedcapturing':
                return $this->buildGenerationTree($node->getChild(0));
            case '#alternation':
            case '#class':
                $generatorNode = new OrGeneratorNode();
                foreach ($node->getChildren() as $child) {
                    $generatorNode->addChild($this->buildGenerationTree($child));
                }
                return $generatorNode;
            case '#concatenation':
                $generatorNode = new AndGeneratorNode();
                foreach ($node->getChildren() as $child) {
                    $generatorNode->addChild($this->buildGenerationTree($child));
                }
                return $generatorNode;
            case '#quantification':
                $xy = $node->getChild(1)->getValueValue();
                $x = 0;
                $y = 0;
                switch ($node->getChild(1)->getValueToken()) {
                    case 'zero_or_one':
                        $y = 1;
                        break;
                    case 'zero_or_more':
                        $y = $this->limitQuantification; // noticed
                        $this->hasMoreQuantification = true;
                        break;
                    case 'one_or_more':
                        $x = 1;
                        $y = $this->limitQuantification; // noticed
                        $this->hasMoreQuantification = true;
                        break;
                    case 'exactly_n':
                        $x = $y = (int)substr($xy, 1, -1);
                        break;
                    case 'n_to_m':
                        $xy = explode(',', substr($xy, 1, -1));
                        $x = (int)trim($xy[0]);
                        $y = (int)trim($xy[1]);
                        break;
                    case 'n_or_more':
                        $xy = explode(',', substr($xy, 1, -1));
                        $x = (int)trim($xy[0]);
                        $y = $x + $this->limitQuantification; // noticed
                        $this->hasMoreQuantification = true;
                        break;
                }

                if ($x && $y) {
                    $this->hasQuantification = true;
                    $generatorNode = new OrGeneratorNode();
                    for ($i = $x; $i <= $y; ++$i) {
                        $childGeneratorNode = new AndGeneratorNode();
                        for ($j = 0; $j < $i; ++$j) {
                            $childGeneratorNode->addChild($this->buildGenerationTree($node->getChild(0)));
                        }
                        $generatorNode->addChild($childGeneratorNode);
                    }
                    return $generatorNode;
                }
                return '';
            case '#negativeclass':
                $abandonedChars = [];
                foreach ($node->getChildren() as $child) {
                    array_push($abandonedChars, ...$this->buildGenerationTree($child)->getValuesOfLeafChildren());
                }

                $generatorNode = new OrGeneratorNode();
                foreach (range(32, 126) as $charCode) {
                    if (!in_array($char = Ustring::fromCode($charCode), $abandonedChars)) {
                        $generatorNode->addChild($char);
                    }
                }
                return $generatorNode;
            case '#range':
                $left = Ustring::toCode($this->buildGenerationTree($node->getChild(0)));
                $right = Ustring::toCode($this->buildGenerationTree($node->getChild(1)));

                $generatorNode = new OrGeneratorNode();
                for ($i = $left; $i <= $right; ++$i) {
                    $generatorNode->addChild(Ustring::fromCode($i));
                }
                return $generatorNode;
            case 'token':
                $nodeValue = $node->getValueValue();
                switch ($node->getValueToken()) {
                    case 'character':
                        $nodeValue = ltrim($nodeValue, '\\');
                        switch ($nodeValue) {
                            case 'a':
                                return "\a";
                            case 'e':
                                return "\e";
                            case 'f':
                                return "\f";
                            case 'n':
                                return "\n";
                            case 'r':
                                return "\r";
                            case 't':
                                return "\t";
                            default:
                                return Ustring::fromCode(intval(substr($nodeValue, 1)));
                        }
                    case 'dynamic_character':
                        $nodeValue = ltrim($nodeValue, '\\');
                        switch ($nodeValue[0]) {
                            case 'x':
                                return Ustring::fromCode(hexdec(trim($nodeValue, 'x{}')));
                            default:
                                return Ustring::fromCode(octdec($nodeValue));
                        }
                    case 'character_type':
                        $nodeValue = ltrim($nodeValue, '\\');
                        if (in_array($nodeValue, ['s', 'C', 'd', 'h', 'v', 'w'])) {
                            switch ($nodeValue) {
                                case 'C':
                                    return (new OrGeneratorNode())->addChildren(range(0, 127));
                                case 'd':
                                    return (new OrGeneratorNode())->addChildren(range(0, 9));
                                case 'h':
                                    return (new OrGeneratorNode())->addChildren([
                                        Ustring::fromCode(0x0009),
                                        Ustring::fromCode(0x0020),
                                        Ustring::fromCode(0x00a0),
                                    ]);
                                case 'v':
                                    return (new OrGeneratorNode())->addChildren([
                                        Ustring::fromCode(0x000a),
                                        Ustring::fromCode(0x000b),
                                        Ustring::fromCode(0x000c),
                                        Ustring::fromCode(0x000d),
                                    ]);
                                case 's': // = h + v
                                    return (new OrGeneratorNode())->addChildren([
                                        Ustring::fromCode(0x0009),
                                        Ustring::fromCode(0x0020),
                                        Ustring::fromCode(0x00a0),
                                        Ustring::fromCode(0x000a),
                                        Ustring::fromCode(0x000b),
                                        Ustring::fromCode(0x000c),
                                        Ustring::fromCode(0x000d),
                                    ]);
                                case 'w':
                                    return (new OrGeneratorNode())->addChildren(array_merge(
                                        range(0x41, 0x5a),
                                        range(0x61, 0x7a),
                                        [0x5f]
                                    ));
                                default:
                                    return '?';
                            }
                        }

                        return '?';
                    case 'literal':
                        if ('.' === $nodeValue) {
                            return (new OrGeneratorNode())->addChildren(array_merge(
                                range(0x41, 0x5a),
                                range(0x61, 0x7a),
                                [0x5f]
                            ));
                        }

                        return str_replace('\\\\', '\\', preg_replace('#\\\(?!\\\)#', '', $nodeValue));
                    default:
                        return '';
                }
                break;
            case '#internal_options':
                return '';
            default:
                throw new AppException($this->__transErrorWithModule('token_not_supported'));
        }
    }
}
