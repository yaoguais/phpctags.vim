<?php

namespace PhpCTags\Parser;

class Namespace_
{
    public function parseToken($tokens, $limit = -1)
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

    public function parseType($types, $name, $namespace, $content, $line)
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
        $use = $useFinder->find($alias, $types, $content, $startLine, $line);

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

    public function parseFunction($name, $namespace, $content, $line)
    {
        $types = [Use_::TYPE_NORMAL, Use_::TYPE_FUNCTION];

        return $this->parseType($types, $name, $namespace, $content, $line);
    }

    public function parseConst($name, $namespace, $content, $line)
    {
        $types = [Use_::TYPE_CONSTANT];

        return $this->parseType($types, $name, $namespace, $content, $line);
    }

    public function parseClass($class, $content, $line)
    {
        $root = 0 == strncmp($class, '\\', 1);
        if ($class) {
            $class = trim($class, '\\');
        }
        if (! $class) {
            return [false, null, null];
        }

        if ($root) {
            $parts = explode('\\', $class);

            return [true, array_pop($parts), count($parts) > 0 ? implode('\\', $parts) : null];
        }

        $nsFinder = new \PhpCTags\Finder\Namespace_();
        $nsInfo = $nsFinder->find($content, $line);
        $startLine = 0;
        $namespace = null;
        if ($nsInfo) {
            $namespace = $nsInfo[0];
            $startLine = $nsInfo[1];
        }

        $parts = explode('\\', $class);
        $alias = $parts[0];

        $useFinder = new \PhpCTags\Finder\Use_();
        $use = $useFinder->find($alias, [Use_::TYPE_NORMAL], $content, $startLine, $line);

        if (! $use) {
            if (! $namespace) {
                return [true, array_pop($parts), count($parts) > 0 ? implode('\\', $parts) : null];
            }
            $class = array_pop($parts);
            array_unshift($parts, $namespace);

            return [true, $class, implode('\\', $parts)];
        }

        $ps = explode('\\', trim($use[0], '\\'));
        array_shift($parts);
        $parts = array_merge($ps, $parts);

        return [true, array_pop($parts), count($parts) > 0 ? implode('\\', $parts) : null];
    }

    public function parseMethod($name, $class, $content, $line)
    {
        list($ok, $class, $namespace) = $this->parseClass($class, $content, $line);
        if ($ok) {
            return [$ok, $name, $class, $namespace];
        }

        return [$ok, null, null, null];
    }
}
