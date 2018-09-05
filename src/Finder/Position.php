<?php

namespace PhpCTags\Finder;

class Position
{
    public function find($file, $line, $column, $keyword, $root, $autoload)
    {
        $typeParser = new \PhpCTags\Parser\Type_();
        $content = \PhpCTags\Pool\File::getInstance()->fromFile($file);

        $finder = $typeParser->parse($content, $line, $column, $keyword);

        if ($finder instanceof \PhpCTags\Finder\Position\Variable) {
            $finder->file = $file;

            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Position\Function_) {
            $finder->file = $file;
            $finder->root = $root;
            $finder->autoload = $autoload;

            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Position\Method) {
            $finder->file = $file;
            $finder->root = $root;
            $finder->autoload = $autoload;

            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Position\ClassConstant) {
            $finder->file = $file;
            $finder->root = $root;
            $finder->autoload = $autoload;

            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Position\Constant_) {
            $finder->file = $file;
            $finder->root = $root;
            $finder->autoload = $autoload;

            return $finder->find();
        }

        return null;
    }
}
