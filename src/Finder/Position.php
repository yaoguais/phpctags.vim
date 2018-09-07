<?php

namespace PhpCTags\Finder;

class Position
{
    public function find($file, $line, $column, $keyword, $root, $autoload)
    {
        $root = $this->getRoot($root, $file, $autoload);

        require_once $this->getAutoloadFile($root, $file, $autoload);

        $typeParser = new \PhpCTags\Parser\Type();
        $content = \PhpCTags\Pool\File::getInstance()->fromFile($file);

        $finder = $typeParser->parse($content, $line, $column, $keyword);

        if ($finder instanceof \PhpCTags\Finder\Position\Variable) {
            $finder->file = $file;

            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Position\Function_) {
            $finder->root = $root;

            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Position\Method) {
            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Position\ClassConst) {
            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Position\ClassVar) {
            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Position\Class_) {
            return $finder->find();
        }

        if ($finder instanceof \PhpCTags\Finder\Position\Const_) {
            $finder->root = $root;

            return $finder->find();
        }

        return null;
    }

    public function getRoot($root, $file, $autoload)
    {
        if (! $root) {
            $parser = new \PhpCTags\Parser\Root();
            $root = $parser->parse($file, $autoload);
        }
        if (! $root) {
            throw new \Exception('Finder project root is invalid');
        }

        return $root;
    }

    public function getAutoloadFile($root, $file, $autoload)
    {
        $autoload = realpath($root.DIRECTORY_SEPARATOR.$autoload);
        if (! file_exists($autoload)) {
            throw new \Exception('Finder autoload file is not exist');
        }

        return $autoload;
    }
}
