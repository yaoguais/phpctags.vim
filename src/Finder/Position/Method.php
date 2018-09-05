<?php

namespace PhpCTags\Finder\Position;

class Method implements Finder
{
    public $root;
    public $namespace;
    public $class;
    public $name;
    public $file;
    public $autoload;

    public function validate()
    {
        if (! $this->getRoot()) {
            throw new \Exception('Method Finder root is invalid');
        }
        if (! $this->name) {
            throw new \Exception('Method Finder name is invalid');
        }
        if (! $this->class) {
            throw new \Exception('Method Finder class is invalid');
        }
    }

    public function find()
    {
        $this->validate();

        require_once $this->getAutoloadFile();

        try {
            $class = $this->namespace ? $this->namespace.'\\'.$this->class : $this->class;
            $refMethod = new \ReflectionMethod($class, $this->name);
        } catch (\Exception $e) {
            throw new \Exception('Reflection Method: '.$e->getMessage());
        }

        $file = $refMethod->getFileName();
        if (! file_exists($file)) {
            throw new \Exception("Method Finder file not found: $file");
        }
        $line = $refMethod->getStartLine();
        if ($line <= 0) {
            throw new \Exception("Method Finder line is invalid: $line");
        }

        $rows = file($file);
        $raw = isset($rows[$line - 1]) ? $rows[$line - 1] : null;

        return new \PhpCTags\Position($file, $line, stripos($raw, $this->name) + 1);
    }

    public function getRoot()
    {
        if (! file_exists($this->file)) {
            throw new \Exception("Method Finder file not found: {$this->file}");
        }
        if ($this->root) {
            return $this->root;
        }
        if (! $this->autoload) {
            throw new \Exception('Method Finder autoload is invalid');
        }

        $parser = new \PhpCTags\Parser\Root();
        $this->root = $parser->parse($this->file, $this->autoload);
        if (! $this->root) {
            throw new \Exception('Method Finder root is invalid');
        }

        return $this->root;
    }

    public function getAutoloadFile()
    {
        $autoload = realpath($this->getRoot().DIRECTORY_SEPARATOR.$this->autoload);
        if (! file_exists($autoload)) {
            throw new \Exception('Method Finder autoload file is not exist');
        }

        return $autoload;
    }
}
