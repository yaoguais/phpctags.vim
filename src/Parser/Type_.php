<?php

namespace PhpCTags\Parser;

class Type_
{
    const CONSTANT = 1;
    const VARIABLE = 2;
    const FUNCTION_ = 3;
    const CLASS_ = 4;
    const METHOD = 5;
    const TRAIT_ = 6;
    const INTERFACE_ = 7;

    public function parse($content, $line, $column, $keyword)
    {
        $tokens = \PhpCTags\Pool\Token::getInstance()->fromContent($content);

        $idx = $this->index($tokens, $line, $column, $keyword);
        if ($idx < 0) {
            throw new \Exception("keyword not found: $keyword");
        }

        $varParser = new \PhpCTags\Parser\Variable();
        list($ok) = $varParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Variable();
            $finder->tokens = $tokens;
            $finder->index = $idx;

            return $finder;
        }

        $funcParser = new \PhpCTags\Parser\Function_();
        list($ok, $name, $namespace) = $funcParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Function_();
            $finder->name = $name;
            $finder->namespace = $namespace;

            return $finder;
        }

        $methodParser = new \PhpCTags\Parser\Method();
        list($ok, $name, $class, $namespace) = $methodParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Method();
            $finder->name = $name;
            $finder->class = $class;
            $finder->namespace = $namespace;

            return $finder;
        }

        $classConstParser = new \PhpCTags\Parser\ClassConstant();
        list($ok, $name, $class, $namespace) = $classConstParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\ClassConstant();
            $finder->name = $name;
            $finder->class = $class;
            $finder->namespace = $namespace;

            return $finder;
        }

        $constParser = new \PhpCTags\Parser\Constant_();
        list($ok, $name, $namespace) = $constParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Constant_();
            $finder->name = $name;
            $finder->namespace = $namespace;

            return $finder;
        }

        throw new \Exception("keyword can't be parsed: $keyword");
    }

    public function index($tokens, $line, $column, $keyword)
    {
        $ln = 1;
        $col = 0;
        $str = '';
        for ($i = 0, $l = count($tokens); $i < $l; ++$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            $raw = $data;

            if ($ln > $line) {
                return -1;
            }

            if ($ln == $line) {
                if ($data === $keyword && $column > $col && $column <= $col + strlen($keyword)) {
                    return $i;
                }
                $str .= $raw;
                $col += strlen($raw);
            }
            if (\PhpCTags\Parser\Token::isNewLine($token)) {
                ++$ln;
            }
        }

        return -1;
    }
}
