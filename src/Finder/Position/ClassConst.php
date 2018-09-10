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

        $refClass = $this->getDefinedClassReflection($this->name, $refClass);
        $pattern = sprintf('/const\s+%s\s*=/', $this->name);

        return $this->findAttribute($refClass, $this->name, $pattern);
    }

    public function getDefinedClassReflection($name, $refClass)
    {
        if (version_compare(PHP_VERSION, '7.1.0') >= 0) {
            $refConst = $refClass->getReflectionConstant($name);
            if (! $refConst) {
                $class = $refClass->getName();
                $this->throwException("class '$class' has no defined const: $name");
            }

            return $refConst->getDeclaringClass();
        }

        if (! array_key_exists($name, $refClass->getConstants())) {
            $class = $refClass->getName();
            $this->throwException("class '$class' has no defined const: $name");
        }

        $inParent = false;
        $refParent = $refClass->getParentClass();
        if ($refParent) {
            $inParent = array_key_exists($name, $refParent->getConstants());
        }

        $refInterface = null;
        $interfaces = $refClass->getInterfaces();
        if (count($interfaces) > 0) {
            foreach ($interfaces as $ref) {
                if (array_key_exists($name, $ref->getConstants())) {
                    $refInterface = $ref;
                    break;
                }
            }
        }

        if ($inParent) {
            return $this->getDefinedClassReflection($name, $refParent);
        }

        if ($refInterface) {
            return $this->getDefinedClassReflection($name, $refInterface);
        }

        return $refClass;
    }
}
