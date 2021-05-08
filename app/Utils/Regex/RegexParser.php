<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Regex;

use App\Exceptions\AppException;
use App\Utils\ClassTrait;
use Hoa\Compiler\Llk\Llk;
use Hoa\Compiler\Llk\TreeNode;
use Hoa\File\Read;
use Throwable;

class RegexParser
{
    use ClassTrait;

    protected static $instance;

    /**
     * @return RegexParser
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new RegexParser();
        }
        return static::$instance;
    }

    protected $compiler;

    protected $defaultParsedCallback;

    private function __construct()
    {
        $this->compiler = Llk::load(new Read(base_path('vendor/hoa/regex/Grammar.pp')));
        $this->defaultParsedCallback = function (TreeNode $node, $level) {
            echo str_repeat('--', $level) . $node->getId() . PHP_EOL;
        };
    }

    /**
     * @param $regex
     * @param callable|bool|null $callback
     * @return TreeNode
     * @throws
     */
    public function parse($regex, $callback = null)
    {
        try {
            $parsed = $this->compiler->parse($regex);
            if ($callback !== false) {
                $this->walk($parsed, empty($callback) ? $this->defaultParsedCallback : $callback);
            }
            return $parsed;
        }
        catch (Throwable $exception) {
            throw new AppException($this->__transErrorWithModule('unexpected_token'));
        }
    }

    protected function walk(TreeNode $node, $callback, $level = 0)
    {
        $callback($node, $level);

        foreach ($node->getChildren() as $childrenNode) {
            $this->walk($childrenNode, $callback, $level + 1);
        }
    }
}
