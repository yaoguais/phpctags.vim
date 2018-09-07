<?php

namespace PhpCTags\Finder\Position;

class ClassConst extends Method implements Finder
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

        $refConst = $refClass->getReflectionConstant($this->name);
        if (! $refConst) {
            $this->throwException("const not defined: {$this->name}");
        }

        $refClass = $refConst->getDeclaringClass();
        $pattern = sprintf('/const\s+%s\s*=/', $this->name);

        return $this->findAttribute($refClass, $this->name, $pattern);
    }
}
