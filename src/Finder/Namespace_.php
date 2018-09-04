<?php

namespace PhpCTags\Finder;

class Namespace_
{
    public function find($content, $line)
    {
        $namespaces = \PhpCTags\Pool\Namespace_::getInstance()->fromContent($content);
        for ($i = count($namespaces) - 1; $i >= 0; --$i) {
            $namespace = $namespaces[$i];
            if ($namespace[1] <= $line) {
                return $namespace;
            }
        }

        return null;
    }
}
