<?php

namespace PhpCTags\Parser;

define('T_NEW_LINE', -1);

class Token
{
    public function parse($content)
    {
        if (! $content) {
            throw new \Exception('content is invalid for parsing token');
        }

        $result = [];

        $tokens = token_get_all($content);
        if (! $tokens) {
            throw new \Exception('content has no token');
        }

        foreach ($tokens as $token) {
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;

            if (T_CONSTANT_ENCAPSED_STRING == $name) {
                $result[] = [$name, $data];
                continue;
            }
            if (T_COMMENT == $name) {
                if ('//' === substr($data, 0, 2) || '#' === substr($data, 0, 1)) {
                    $lines = ["\r\n", "\n"];
                    foreach ($lines as $line) {
                        if (substr($data, -strlen($line)) == $line) {
                            $result[] = [$name, substr($data, 0, -strlen($line))];
                            $result[] = [T_NEW_LINE, $line];
                            continue 2;
                        }
                    }
                    $result[] = [$name, $data];
                    continue;
                }
            }

            $split = preg_split('#(\r\n|\n)#', $data, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            foreach ($split as $piece) {
                if ("\r\n" == $piece || "\n" == $piece) {
                    $result[] = [T_NEW_LINE, $piece];
                } else {
                    $result[] = is_array($token) ? [$name, $piece] : $piece;
                }
            }
        }

        return $result;
    }

    public function parseRange($tokens, $types)
    {
        $stack = [];
        $ranges = [];
        $line = 1;

        foreach ($tokens as $i => $token) {
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;

            if (\PhpCTags\Parser\Token::isNewLine($token)) {
                ++$line;
            }

            if (in_array($name, $types)) {
                array_push($stack, [0, $name, $i, $line]);
            } elseif ('{' === $data) {
                array_push($stack, [1, $i]);
            } elseif ('}' === $data) {
                array_pop($stack);
                $l = count($stack);
                if ($l > 0) {
                    $top = $stack[$l - 1];
                    if (0 == $top[0]) {
                        array_pop($stack);
                        $range = [$top[1], $top[2], $i, $top[3], $line];
                        array_unshift($ranges, $range);
                    }
                }
            }
        }

        return $ranges;
    }

    public function isInRange($tokens, $types, $idx)
    {
        $ranges = $this->parseRange($tokens, $types);
        foreach ($ranges as $range) {
            if ($range[1] <= $idx && $idx <= $range[2]) {
                return true;
            }
        }

        return false;
    }

    public static function isNewLine($token)
    {
        return is_array($token) && T_NEW_LINE === $token[0];
    }
}
