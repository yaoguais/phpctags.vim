<?php

namespace PhpCTags\Parser\Type;

class ClassVar extends Class_ implements Parser
{
    public function parse($tokens, $idx, $content, $line)
    {
        $name = is_array($tokens[$idx]) ? $tokens[$idx][0] : null;
        if (T_STRING !== $name) {
            return [false, null, null, null];
        }

        $l = count($tokens);
        $objOp = null;

        for ($i = $idx - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;

            if ('' === trim($data)) {
                continue;
            }

            if (null === $objOp && T_OBJECT_OPERATOR == $name) {
                $objOp = $i;
                continue;
            }

            if ('$this' !== $data || null === $objOp) {
                return [false, null, null, null];
            }
            break;
        }

        for ($i = $idx + 1; $i < $l; ++$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            if ('' === trim($data)) {
                continue;
            }
            if ('(' === $data) {
                return [false, null, null, null];
            }
            break;
        }

        $var = is_array($tokens[$idx]) ? $tokens[$idx][1] : $tokens[$idx];

        $classes = \PhpCTags\Pool\Class_::getInstance()->fromContent($content);
        for ($j = count($classes) - 1; $j >= 0; --$j) {
            if ($classes[$j][3] <= $line) {
                return [true, $var, $classes[$j][0], $classes[$j][1]];
            }
        }

        return [false, null, null, null];
    }
}
