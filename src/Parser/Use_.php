<?php

namespace PhpCTags\Parser;

class Use_
{
    const TYPE_NORMAL = 1;
    const TYPE_FUNCTION = 2;
    const TYPE_CONSTANT = 3;

    public function parseToken($tokens)
    {
        $uses = [];
        $line = 1;
        $raw = null;
        $useLine = -1;
        for ($i = 0, $l = count($tokens); $i < $l; ++$i) {
            $token = $tokens[$i];
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;

            if (\PhpCTags\Parser\Token::isNewLine($token)) {
                ++$line;
            }

            if (T_USE === $name) {
                $raw = '';
                $useLine = $line;
                continue;
            }

            if (null !== $raw) {
                if (';' === $data) {
                    foreach ($this->parseUse($raw, $useLine) as $alias => $us) {
                        foreach ($us as $u) {
                            $uses[$alias][] = $u;
                        }
                    }
                    $raw = null;
                    $useLine = -1;
                } else {
                    $raw .= $data;
                }
            }
        }

        return $uses;
    }

    public function parseUse($raw, $line)
    {
        $uses = [];

        $us = explode(',', $raw);
        $fs = explode('{', array_shift($us));

        $type = null;
        $prefix = null;

        $fns = preg_split('/\s/', $fs[0], -1, PREG_SPLIT_NO_EMPTY);
        if (count($fns) > 1) {
            $type = $this->parseType($fns[0]);
        }
        $fn = count($fs);
        if ($fn > 1) {
            $prefix = $fns[count($fns) - 1];
        }
        array_unshift($us, $fs[$fn - 1]);

        foreach ($us as $u) {
            $cs = preg_split('/\s/', $u, -1, PREG_SPLIT_NO_EMPTY);
            $lt = array_pop($cs);
            if ('}' != $lt) {
                array_push($cs, $lt);
            }
            $cn = count($cs);
            if (1 == $cn) {
                $name = $prefix ? $prefix.$cs[0] : $cs[0];
                $pos = strrpos($name, '\\');
                $alias = false === $pos ? strtolower($name) : strtolower(substr($name, $pos + 1));
                $t = null !== $type ? $type : self::TYPE_NORMAL;
                $uses[$alias][] = [$name, $t, $line];
            } elseif (2 == $cn) {
                $name = $prefix ? $prefix.$cs[1] : $cs[1];
                $pos = strrpos($name, '\\');
                $alias = false === $pos ? strtolower($name) : strtolower(substr($name, $pos + 1));
                $t = $this->parseType($cs[0]);
                $uses[$alias][] = [$name, $t, $line];
            } elseif (3 == $cn) {
                if ('as' !== $cs[1]) {
                    throw new \Exception("invalid use statement: $u");
                }
                $name = $prefix ? $prefix.$cs[0] : $cs[0];
                $alias = strtolower($cs[2]);
                $t = null !== $type ? $type : self::TYPE_NORMAL;
                $uses[$alias][] = [$name, $t, $line];
            } elseif (4 == $cn) {
                if ('as' !== $cs[2]) {
                    throw new \Exception("invalid use statement: $u");
                }
                $name = $prefix ? $prefix.$cs[1] : $cs[1];
                $alias = strtolower($cs[3]);
                $t = $this->parseType($cs[0]);
                $uses[$alias][] = [$name, $t, $line];
            }
        }

        return $uses;
    }

    public function parseType($name)
    {
        if ('function' == $name) {
            return self::TYPE_FUNCTION;
        } elseif ('const' == $name) {
            return self::TYPE_CONSTANT;
        } else {
            return self::TYPE_NORMAL;
        }
    }
}
