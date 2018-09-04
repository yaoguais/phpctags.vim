<?php

namespace PhpCTags\Parser;

class Position
{
    public function parse($file, $line, $column, $keyword, $root, $autoload)
    {
        $typeParser = new \PhpCTags\Parser\Type_();
        $content = \PhpCTags\Pool\File::getInstance()->fromFile($file);

        $finder = $typeParser->parse($content, $line, $column, $keyword);

        if ($finder instanceof \PhpCTags\Finder\Variable) {
            $finder->file = $file;

            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Function_) {
            $finder->file = $file;
            $finder->root = $root;
            $finder->autoload = $autoload;

            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Method) {
            $finder->file = $file;
            $finder->root = $root;
            $finder->autoload = $autoload;

            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\ClassConstant) {
            $finder->file = $file;
            $finder->root = $root;
            $finder->autoload = $autoload;

            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Constant_) {
            $finder->file = $file;
            $finder->root = $root;
            $finder->autoload = $autoload;

            return $finder->find();
        }

        return null;
    }
}
