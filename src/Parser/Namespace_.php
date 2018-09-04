<?php

namespace PhpCTags\Parser;

class Namespace_
{
    public function parse($tokens, $limit = -1)
    {
        $namespaces = [];
        $namespace = null;
        $line = 1;
        for ($i = 0, $l = count($tokens); $i < $l; ++$i) {
            $token = $tokens[$i];
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;

            if (\PhpCTags\Parser\Token::isNewLine($token)) {
                ++$line;
            }

            if (T_NAMESPACE === $name) {
                $namespace = '';
                continue;
            }
            if ('' === $namespace && '' === trim($data)) {
                continue;
            }
            if ('' === trim($data) || ';' === $data || '{' === $data) {
                if ($namespace) {
                    $namespaces[] = [$namespace, $line];
                    if ($limit > 0 && count($namespaces) >= $limit) {
                        break;
                    }
                }
                $namespace = null;
            }
            if (null !== $namespace) {
                $namespace .= $data;
            }
        }

        return $namespaces;
    }
}
