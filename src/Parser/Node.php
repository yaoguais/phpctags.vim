<?php

namespace PhpCTags\Parser;

class Node
{
    public function parse($content)
    {
        $options = ['usedAttributes' => ['comments', 'startLine', 'endLine', 'startTokenPos',
            'endTokenPos', 'startFilePos', 'endFilePos', ]];
        $lexer = new \PhpParser\Lexer($options);
        $parser = (new \PhpParser\ParserFactory())->create(\PhpParser\ParserFactory::PREFER_PHP7, $lexer);
        $nodes = $parser->parse($content);
        if (! $nodes) {
            throw new \Exception('parse to ast nodes failed');
        }

        return $nodes;
    }
}
