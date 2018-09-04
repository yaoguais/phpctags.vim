<?php

namespace PhpCTags\Finder;

class Use_
{
    public function find($alias, $types, $content, $startLine, $endLine)
    {
        $uses = \PhpCTags\Pool\Use_::getInstance()->fromContent($content);
        $key = strtolower($alias);

        if (array_key_exists($key, $uses)) {
            for ($i = count($uses[$key]) - 1; $i >= 0; --$i) {
                $use = $uses[$key][$i];
                if (in_array($use[1], $types) && $startLine <= $use[2] && $use[2] <= $endLine) {
                    return $use;
                }
            }
        }

        return null;
    }
}
