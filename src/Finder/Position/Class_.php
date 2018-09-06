<?php

namespace PhpCTags\Finder\Position;

class Class_ implements Finder
{
    public $root;
    public $namespace;
    public $name;
    public $file;
    public $autoload;

    public function validate()
    {
        if (! $this->getRoot()) {
            throw new \Exception('Class Finder root is invalid');
        }
        if (! $this->name) {
            throw new \Exception('Class Finder name is invalid');
        }
    }

    public function getRoot()
    {
        if (! file_exists($this->file)) {
            throw new \Exception("Class Finder file not found: {$this->file}");
        }
        if ($this->root) {
            return $this->root;
        }
        if (! $this->autoload) {
            throw new \Exception('Class Finder autoload is invalid');
        }

        $parser = new \PhpCTags\Parser\Root();
        $this->root = $parser->parse($this->file, $this->autoload);
        if (! $this->root) {
            throw new \Exception('Class Finder root is invalid');
        }

        return $this->root;
    }

    public function getAutoloadFile()
    {
        $autoload = realpath($this->getRoot().DIRECTORY_SEPARATOR.$this->autoload);
        if (! file_exists($autoload)) {
            throw new \Exception('Class Finder autoload file is not exist');
        }

        return $autoload;
    }

    public function find()
    {
        $this->validate();

        require_once $this->getAutoloadFile();

        $class = $this->namespace ? $this->namespace.'\\'.$this->name : $this->name;
        try {
            $refClass = new \ReflectionClass($class);
        } catch (\Exception $e) {
            throw new \Exception('Reflection Class: '.$e->getMessage());
        }

        $file = $refClass->getFileName();
        if (! file_exists($file)) {
            throw new \Exception("Class Finder file not found: $file");
        }
        $line = $refClass->getStartLine();
        if ($line <= 0) {
            throw new \Exception("Class Finder line is invalid: $line");
        }

        $rows = file($file);
        $raw = isset($rows[$line - 1]) ? $rows[$line - 1] : null;

        return new \PhpCTags\Position($file, $line, stripos($raw, $this->name) + 1);
    }
}
