<?php

namespace PhpCTags\Parser;

class Variable
{
    public function parse($tokens, $idx)
    {
        $token = $tokens[$idx];
        $name = is_array($token) ? $token[0] : null;
        if (T_VARIABLE === $name) {
            return [true, $token];
        }

        return [false, null];
    }
}
