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

        $objOpIdx = null;
        for ($i = $idx - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;

            if ('' === trim($data)) {
                continue;
            }

            if (null === $objOpIdx && T_OBJECT_OPERATOR == $name) {
                $objOpIdx = $i;
            }
            break;
        }

        if (! $objOpIdx) {
            return [false, null, null, null];
        }

        list($ok, $caller, $class, $namespace) = $this->parseCaller($tokens, $objOpIdx, $content, $line);
        if (! $ok || '$this' != $caller) {
            return [false, null, null, null];
        }

        $var = is_array($tokens[$idx]) ? $tokens[$idx][1] : $tokens[$idx];

        return [true, $var, $class, $namespace];
    }
}
