<?php

namespace PhpCTags\Parser;

class Function_
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

        return $this->parseName($name, $namespace, $content, $line);
    }

    public function parseName($name, $namespace, $content, $line)
    {
        $root = 0 == strncmp($namespace, '\\', 1);
        if ($namespace) {
            $namespace = trim($namespace, '\\');
            $namespace = $namespace ? $namespace : null;
        }

        if ($root) {
            return [true, $name, $namespace];
        }

        $startLine = 0;
        $nsFinder = new \PhpCTags\Finder\Namespace_();
        $nsInfo = $nsFinder->find($content, $line);
        if ($nsInfo) {
            $startLine = $nsInfo[1];
        }

        if (! $namespace) {
            $alias = $name;
        } else {
            $parts = explode('\\', $namespace);
            $alias = $parts[0];
        }

        $useFinder = new \PhpCTags\Finder\Use_();
        $use = $useFinder->find($alias,
            [\PhpParser\Node\Stmt\Use_::TYPE_NORMAL, \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION],
            $content, $startLine, $line
        );

        if (! $use) {
            if (! $nsInfo) {
                if (! $namespace) {
                    return [true, $name, null];
                } else {
                    return [true, $name, $namespace];
                }
            } else {
                if (! $namespace) {
                    return [true, $name, $nsInfo[0]];
                } else {
                    return [true, $name, $nsInfo[0].'\\'.$namespace];
                }
            }
        }

        if (! $namespace) {
            $parts = explode('\\', trim($use[0], '\\'));
        } else {
            $parts = explode('\\', $namespace);
            array_shift($parts);
            array_unshift($parts, trim($use[0], '\\'));
            array_push($parts, $name);
        }

        return [true, array_pop($parts), $parts ? implode('\\', $parts) : null];
    }
}
