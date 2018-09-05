<?php

namespace PhpCTags\Parser\Type;

class Const_ implements Parser
{
    public function parse($tokens, $idx, $content, $line)
    {
        $token = $tokens[$idx];
        $name = is_array($token) ? $token[0] : null;
        if (T_STRING != $name) {
            return [false, null, null];
        }

        $full = null;
        // glue left and right, such as hit B for A\B\C.
        for ($i = $idx; $i >= 0; --$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            if ('\\' == $data || preg_match('/^[A-Za-z_]+[A-Za-z0-9_]*$/', $data)) {
                $full = $data.$full;
                continue;
            }
            break;
        }
        $l = count($tokens);
        for ($i = $idx + 1; $i < $l; ++$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            if ('\\' == $data || preg_match('/^[A-Za-z_]+[A-Za-z0-9_]*$/', $data)) {
                $full = $full.$data;
                continue;
            }
            break;
        }

        if (! $full) {
            return [false, null, null];
        }

        if (false !== strpos($full, '\\\\')) {
            return [false, null, null];
        }

        $parts = explode('\\', $full);
        $name = array_pop($parts);
        $namespace = count($parts) > 0 ? implode('\\', $parts) : null;
        $nsParser = new \PhpCTags\Parser\Namespace_();

        return $nsParser->parseConst($name, $namespace, $content, $line);
    }
}
