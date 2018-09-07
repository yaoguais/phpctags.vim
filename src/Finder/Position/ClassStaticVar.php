<?php

namespace PhpCTags\Finder\Position;

class ClassStaticVar extends Method implements Finder
{
    public function find()
    {
        $this->validate();

        $class = $this->namespace ? $this->namespace.'\\'.$this->class : $this->class;
        try {
            $refClass = new \ReflectionClass($class);
        } catch (\Exception $e) {
            $this->throwException('Reflection Class: '.$e->getMessage());
        }

        $name = ltrim($this->name, '$');
        $refVar = $refClass->getProperty($name);
        if (! $refVar) {
            $this->throwException("property not defined: {$this->name}");
        }

        $refClass = $refVar->getDeclaringClass();
        $pattern = sprintf('/static\s+\$%s\s*[=;]/', $name);

        return $this->findAttribute($refClass, $this->name, $pattern);
    }
}
