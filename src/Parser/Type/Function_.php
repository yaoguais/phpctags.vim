<?php

namespace PhpCTags\Parser\Type;

class Function_ implements Parser
{
    public function parse($tokens, $idx, $content, $line)
    {
        // check whether is function
        $l = count($tokens);
        $ok = false;
        // function name should has a next "("
        for ($i = $idx + 1; $i < $l; ++$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            if ('' === trim($data)) {
                continue;
            }
            if ('(' === $data) {
                $ok = true;
            }
            break;
        }
        if (! $ok) {
            return [false, null, null];
        }

        $namespace = null;
        for ($i = $idx - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            if ('\\' == $data || preg_match('/^[A-Za-z_]+[A-Za-z0-9_]*$/', $data)) {
                $namespace = $data.$namespace;
                continue;
            }
            break;
        }

        for ($i = $idx - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            if ('' === trim($data)) {
                continue;
            }
            $name = is_array($token) ? $token[0] : null;
            if (T_OBJECT_OPERATOR === $name || T_NEW === $name || T_PAAMAYIM_NEKUDOTAYIM == $name) {
                return [false, null, null];
            }
            break;
        }

        $name = is_array($tokens[$idx]) ? $tokens[$idx][1] : $tokens[$idx];
        $nsParser = new \PhpCTags\Parser\Namespace_();

        return $nsParser->parseFunction($name, $namespace, $content, $line);
    }
}
