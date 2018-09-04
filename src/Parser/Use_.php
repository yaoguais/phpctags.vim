<?php

namespace PhpCTags\Parser;

class Use_ extends \PhpParser\NodeVisitorAbstract
{
    protected $uses;

    public function parse($nodes)
    {
        $this->uses = [];
        $traverser = new \PhpParser\NodeTraverser();
        $traverser->addVisitor($this);
        $traverser->traverse($nodes);

        return $this->uses;
    }

    public function enterNode(\PhpParser\Node $node)
    {
        $class = get_class($node);
        if ('PhpParser\Node\Stmt\Use_' == $class || 'PhpParser\Node\Stmt\GroupUse' == $class) {
            $this->parseUse($node);
        }
    }

    public function parseUse(\PhpParser\Node $node)
    {
        $line = $node->getAttribute('startLine');
        $prefix = property_exists($node, 'prefix') ? implode('\\', $node->prefix->parts) : null;
        foreach ($node->uses as $use) {
            $parts = $use->name->parts;
            if (! is_null($prefix)) {
                array_unshift($parts, $prefix);
            }
            $name = implode('\\', $parts);
            $alias = is_null($use->alias) ? array_pop($parts) :
                (is_string($use->alias) ? $use->alias : $use->alias->name);
            $key = strtolower($alias);
            $type = \PhpParser\Node\Stmt\Use_::TYPE_UNKNOWN != $use->type ? $use->type : $node->type;
            if (\PhpParser\Node\Stmt\Use_::TYPE_UNKNOWN != $type) {
                $this->uses[$key][] = [$name, $type, $line];
            }
        }
    }
}
