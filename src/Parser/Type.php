<?php

namespace PhpCTags\Parser;

class Type
{
    public function parse($content, $line, $column, $keyword)
    {
        $tokens = \PhpCTags\Pool\Token::getInstance()->fromContent($content);

        $idx = $this->index($tokens, $line, $column, $keyword);
        if ($idx < 0) {
            throw new \Exception("keyword not found: $keyword");
        }

        $funcParser = new \PhpCTags\Parser\Type\Function_();
        list($ok, $name, $namespace) = $funcParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Position\Function_();
            $finder->name = $name;
            $finder->namespace = $namespace;

            return $finder;
        }

        $methodParser = new \PhpCTags\Parser\Type\Method();
        list($ok, $name, $class, $namespace) = $methodParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Position\Method();
            $finder->name = $name;
            $finder->class = $class;
            $finder->namespace = $namespace;

            return $finder;
        }

        $classConstParser = new \PhpCTags\Parser\Type\ClassConst();
        list($ok, $name, $class, $namespace) = $classConstParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Position\ClassConst();
            $finder->name = $name;
            $finder->class = $class;
            $finder->namespace = $namespace;

            return $finder;
        }

        $classVarParser = new \PhpCTags\Parser\Type\ClassVar();
        list($ok, $name, $class, $namespace) = $classVarParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Position\ClassVar();
            $finder->name = $name;
            $finder->class = $class;
            $finder->namespace = $namespace;

            return $finder;
        }

        $classStaticVarParser = new \PhpCTags\Parser\Type\ClassStaticVar();
        list($ok, $name, $class, $namespace) = $classStaticVarParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Position\ClassStaticVar();
            $finder->name = $name;
            $finder->class = $class;
            $finder->namespace = $namespace;

            return $finder;
        }

        $classParser = new \PhpCTags\Parser\Type\Class_();
        list($ok, $name, $namespace) = $classParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Position\Class_();
            $finder->name = $name;
            $finder->namespace = $namespace;

            return $finder;
        }

        $constParser = new \PhpCTags\Parser\Type\Const_();
        list($ok, $name, $namespace) = $constParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Position\Const_();
            $finder->name = $name;
            $finder->namespace = $namespace;

            return $finder;
        }

        $varParser = new \PhpCTags\Parser\Type\Variable();
        list($ok) = $varParser->parse($tokens, $idx, $content, $line);
        if ($ok) {
            $finder = new \PhpCTags\Finder\Position\Variable();
            $finder->tokens = $tokens;
            $finder->index = $idx;

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
