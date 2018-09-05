<?php

namespace PhpCTags\Finder\Position;

class ClassConst extends Method implements Finder
{
    public function find()
    {
        $this->validate();

        require_once $this->getAutoloadFile();

        $class = $this->namespace ? $this->namespace.'\\'.$this->class : $this->class;
        try {
            $refClass = new \ReflectionClass($class);
        } catch (\Exception $e) {
            throw new \Exception('Reflection Class: '.$e->getMessage());
        }

        $file = $refClass->getFileName();
        if (! file_exists($file)) {
            throw new \Exception("Class Const Finder file not found: $file");
        }
        $line = $refClass->getStartLine();
        if ($line <= 0) {
            throw new \Exception("Class Const Finder line is invalid: $line");
        }

        $content = file_get_contents($file);
        $startLine = null;
        $classes = \PhpCTags\Pool\Class_::getInstance()->fromContent($content);
        foreach ($classes as $v) {
            if ($v[0] == $this->class && $v[1] == $this->namespace) {
                $startLine = $v[2];
                break;
            }
        }

        if (! $startLine) {
            throw new \Exception("Class Const Finder class not found: $class");
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
            throw new \Exception("Class Const Finder no const found: $class::{$this->name}");
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

        throw new \Exception("Class Const Finder no avaiable const: $class::{$this->name}");
    }
}
