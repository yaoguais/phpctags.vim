<?php

namespace PhpCTags\Finder\Position;

class Method extends Class_ implements Finder
{
    public $class;

    public function validate()
    {
        parent::validate();

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
}
