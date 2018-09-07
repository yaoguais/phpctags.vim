<?php

namespace PhpCTags\Parser;

class Class_
{
    public function parseToken($tokens, $limit = -1)
    {
        $classes = [];
        $class = null;
        $line = 1;
        $type = null;
        $classLine = -1;
        for ($i = 0, $l = count($tokens); $i < $l; ++$i) {
            $token = $tokens[$i];
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;

            if (\PhpCTags\Parser\Token::isNewLine($token)) {
                ++$line;
            }

            if (T_CLASS === $name || T_TRAIT == $name || T_INTERFACE == $name) {
                // skip ClassName::class
                for ($j = $i - 1; $j >= 0; --$j) {
                    $t = $tokens[$j];
                    $n = is_array($t) ? $t[0] : null;
                    $d = is_array($t) ? $t[1] : $t;
                    if ('' === trim($d)) {
                        continue;
                    }
                    if (T_PAAMAYIM_NEKUDOTAYIM == $n) {
                        continue 2;
                    }
                    break;
                }

                $class = '';
                $type = $name;
                $classLine = $line;
                continue;
            }

            if ('' === $class && '' === trim($data)) {
                continue;
            }
            if ('' === trim($data) || '{' === $data) {
                if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $class)) {
                    $classes[] = [$class, $type, $classLine];
                    if ($limit > 0 && count($class) >= $limit) {
                        break;
                    }
                }
                $class = null;
                $type = null;
                $classLine = -1;
            }
            if (null !== $class) {
                $class .= $data;
            }
        }

        return $this->parseNamespace($tokens, $classes);
    }

    public function parseNamespace($tokens, $classes)
    {
        $nsParser = new \PhpCTags\Parser\Namespace_();
        $namespaces = $nsParser->parseToken($tokens);

        $results = [];
        foreach ($classes as $class) {
            $namespace = null;
            for ($i = count($namespaces) - 1; $i >= 0; --$i) {
                if ($namespaces[$i][1] <= $class[2]) {
                    $namespace = $namespaces[$i][0];
                    break;
                }
            }
            $results[] = [$class[0], $namespace, $class[1], $class[2]];
        }

        return $results;
    }
}
