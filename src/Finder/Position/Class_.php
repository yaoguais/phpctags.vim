<?php

namespace PhpCTags\Finder\Position;

class Class_ extends BaseFinder implements Finder
{
    public $namespace;
    public $name;

    public function validate()
    {
        if (! $this->name) {
            $this->throwException('name is invalid');
        }
    }

    public function find()
    {
        $this->validate();

        $class = $this->namespace ? $this->namespace.'\\'.$this->name : $this->name;
        try {
            $refClass = new \ReflectionClass($class);
        } catch (\Exception $e) {
            $this->throwException('Reflection Class: '.$e->getMessage());
        }

        $file = $refClass->getFileName();
        if (! file_exists($file)) {
            $this->throwException("file not found: $file");
        }
        $line = $refClass->getStartLine();
        if ($line <= 0) {
            $this->throwException("line is invalid: $line");
        }

        $rows = file($file);
        $raw = isset($rows[$line - 1]) ? $rows[$line - 1] : null;

        return new \PhpCTags\Position($file, $line, stripos($raw, $this->name) + 1);
    }
}
