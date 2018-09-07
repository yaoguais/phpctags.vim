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

        $namespace = $refClass->getNamespaceName();
        $namespace = $namespace ? $namespace : null;
        $class = $refClass->getShortName();

        $file = $refClass->getFileName();
        if (! file_exists($file)) {
            $this->throwException("file not found: $file");
        }
        $line = $refClass->getStartLine();
        if ($line <= 0) {
            $this->throwException("line is invalid: $line");
        }

        $content = file_get_contents($file);
        $startLine = null;
        $classes = \PhpCTags\Pool\Class_::getInstance()->fromContent($content);
        foreach ($classes as $v) {
            if ($v[0] == $class && $v[1] == $namespace) {
                $startLine = $v[3];
                break;
            }
        }

        if (! $startLine) {
            $this->throwException("class not found: $class");
        }

        $tokens = \PhpCTags\Pool\Token::getInstance()->fromContent($content);
        $tokenParser = new \PhpCTags\Parser\Token();
        $ranges = $tokenParser->parseRange($tokens, [T_CLASS, T_TRAIT, T_INTERFACE, T_FUNCTION]);

        $lines = [];
        foreach ($ranges as $range) {
            if (T_FUNCTION != $range[0] && $range[3] == $startLine) {
                for ($i = $range[3]; $i <= $range[4]; ++$i) {
                    $lines[$i] = true;
                }
            } elseif (count($lines) > 0) {
                for ($i = $range[3]; $i <= $range[4]; ++$i) {
                    unset($lines[$i]);
                }
            }
        }

        if (0 == count($lines)) {
            $this->throwException("no const found: $class::{$this->name}");
        }

        $rows = explode("\n", $content);
        $pattern = sprintf('/const\s+%s\s*=/', $this->name);
        foreach ($lines as $line => $_) {
            if (isset($rows[$line - 1])) {
                $raw = $rows[$line - 1];
                if (preg_match($pattern, $raw)) {
                    return new \PhpCTags\Position($file, $line, stripos($raw, $this->name) + 1);
                }
            }
        }

        $this->throwException("no avaiable const: $class::{$this->name}");
    }
}
