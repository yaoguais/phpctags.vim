<?php

namespace PhpCTags\Parser\Type;

class ClassStaticVar extends Method implements Parser
{
    public function parse($tokens, $idx, $content, $line)
    {
        $name = is_array($tokens[$idx]) ? $tokens[$idx][0] : null;
        if (T_VARIABLE !== $name) {
            return [false, null, null, null];
        }

        $l = count($tokens);
        for ($i = $idx + 1; $i < $l; ++$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            if ('' === trim($data)) {
                continue;
            }
            if ('(' !== $data) {
                break;
            }

            return [false, null, null, null];
        }

        for ($i = $idx - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            if ('' === trim($data)) {
                continue;
            }
            $name = is_array($token) ? $token[0] : null;

            if (T_PAAMAYIM_NEKUDOTAYIM == $name) {
                return $this->parsePaamayimNekudotayim($tokens, $i, $tokens[$idx], $content, $line);
            }
            break;
        }

        return [false, null, null, null];
    }
}
