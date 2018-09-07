<?php

namespace PhpCTags\Parser\Type;

class Class_ implements Parser
{
    public function parse($tokens, $idx, $content, $line)
    {
        $name = is_array($tokens[$idx]) ? $tokens[$idx][0] : null;
        if (T_STRING !== $name && T_STATIC !== $name) {
            return [false, null, null];
        }

        $isClass = false;
        $class = null;
        $l = count($tokens);

        for ($i = $idx; $i >= 0; --$i) {
            $token = $tokens[$i];
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;

            if ('' === trim($data)) {
                continue;
            }

            if (T_NEW == $name || T_INSTANCEOF == $name ||
                T_USE == $name || T_CLASS == $name ||
                T_TRAIT == $name || T_INTERFACE == $name ||
                T_EXTENDS == $name || T_IMPLEMENTS === $name) {
                $isClass = true;
                if (T_USE == $name) {
                    $class = '\\'.$class;
                }
                break;
            }

            if ('\\' == $data || preg_match('/^[A-Za-z_]+[A-Za-z0-9_]*$/', $data)) {
                $class = $data.$class;
            } else {
                break;
            }
        }

        if (! $isClass) {
            $raws = [];
            for (; $i >= 0; --$i) {
                $token = $tokens[$i];
                $name = is_array($token) ? $token[0] : null;
                $data = is_array($token) ? $token[1] : $token;

                if (T_COMMENT == $name || T_DOC_COMMENT == $name) {
                    continue;
                }

                if (T_CLASS == $name) {
                    break;
                }

                if (T_IMPLEMENTS == $name) {
                    $raw = implode('', $raws);
                    if (preg_match('/^\s*([A-Za-z_][A-Za-z0-9_]*\s*\,?\s*)+$/s', $raw)) {
                        $isClass = true;
                    }
                    break;
                }

                $raws[] = $data;
            }
        }

        for ($i = $idx + 1; $i < $l; ++$i) {
            $token = $tokens[$i];
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;

            if ('' === trim($data)) {
                continue;
            }

            if (T_PAAMAYIM_NEKUDOTAYIM == $name || T_EXTENDS == $name || T_IMPLEMENTS == $name) {
                $isClass = true;
                break;
            }

            if ('\\' == $data || preg_match('/^[A-Za-z_]+[A-Za-z0-9_]*$/', $data)) {
                $class = $class.$data;
            } else {
                break;
            }
        }

        if (! $isClass || ! $class) {
            return [false, null, null];
        }

        return $this->parseNamespace($class, $content, $line);
    }

    public function parseNamespace($name, $content, $line)
    {
        $class = null;
        $classes = \PhpCTags\Pool\Class_::getInstance()->fromContent($content);
        for ($j = count($classes) - 1; $j >= 0; --$j) {
            if ($classes[$j][3] <= $line) {
                $class = $classes[$j];
                break;
            }
        }

        if ('self' == $name || 'static' == $name) {
            if ($class) {
                return [true, $class[0], $class[1]];
            }

            return [false, null, null];
        }

        if ('parent' == $name) {
            if ($class) {
                list($name, $namespace) = $this->parseParent($class[0], $class[1]);

                return [true, $name, $namespace];
            }

            return [false, null, null];
        }

        $nsParser = new \PhpCTags\Parser\Namespace_();

        return $nsParser->parseClass($name, $content, $class ? $class[3] : $line);
    }

    public function parseParent($name, $namespace)
    {
        $class = $namespace ? $namespace.'\\'.$name : $name;
        $refClass = new \ReflectionClass($class);

        $refParent = $refClass->getParentClass();
        if (! $refParent) {
            throw new \Exception("Class has no parent class: $class");
        }

        $name = $refParent->getShortName();
        $namespace = $refParent->getNamespaceName();

        return [$name, $namespace ? $namespace : null];
    }
}
