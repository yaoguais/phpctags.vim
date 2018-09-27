<?php

namespace PhpCTags\Parser\Type;

class Method extends Class_ implements Parser
{
    public function parse($tokens, $idx, $content, $line)
    {
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
            } elseif (T_OBJECT_OPERATOR) {
                return $this->parseCallOnObject($tokens, $i, $tokens[$idx], $content, $line);
            }
            break;
        }

        return [false, null, null, null];
    }

    public function parsePaamayimNekudotayim($tokens, $idx, $hitToken, $content, $line)
    {
        list($ok, $caller, $class, $namespace) = $this->parseCaller($tokens, $idx, $content, $line);
        if (! $ok || '$this' == $caller) {
            return [false, null, null, null];
        }

        $method = is_array($hitToken) ? $hitToken[1] : $hitToken;
        if ('self' == $caller || 'static' == $caller || 'parent' == $caller) {
            return [true, $method, $class, $namespace];
        }

        $nsParser = new \PhpCTags\Parser\Namespace_();
        $caller = $namespace ? $namespace.'\\'.$class : $class;

        return $nsParser->parseMethod($method, $caller, $content, $line);
    }

    public function parseCallOnObject($tokens, $idx, $hitToken, $content, $line)
    {
        $method = is_array($hitToken) ? $hitToken[1] : $hitToken;

        $caller = null;
        for ($i = $idx - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            if (null === $caller && '' === trim($data)) {
                continue;
            }

            $name = is_array($token) ? $token[0] : null;
            if (T_VARIABLE === $name) {
                if ('$this' === $data) {
                    $classes = \PhpCTags\Pool\Class_::getInstance()->fromContent($content);
                    for ($j = count($classes) - 1; $j >= 0; --$j) {
                        if ($classes[$j][3] <= $line) {
                            return [true, $method, $classes[$j][0], $classes[$j][1]];
                        }
                    }

                    return [false, null, null, null];
                }

                return $this->parseCallOnVariable($tokens, $i, $hitToken, $content, $line);
            }
            break;
        }

        if (! $caller) {
            return [false, null, null, null];
        }

        return [false, null, null, null];
    }

    public function parseCallOnVariable($tokens, $idx, $hitToken, $content, $line)
    {
        $varName = is_array($tokens[$idx]) ? $tokens[$idx][1] : $tokens[$idx];

        for ($i = $idx - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;
            if (T_VARIABLE == $name && $data == $varName) {
                $result = $this->parseAssignVariable($tokens, $i, $idx, $hitToken, $content, $line);
                if ($result[0]) {
                    return $result;
                }
                $result = $this->parseAssignNewClass($tokens, $i, $idx, $hitToken, $content, $line);
                if ($result[0]) {
                    return $result;
                }
            }
            if (T_FUNCTION == $name) {
                break;
            }
        }

        return [false, null, null, null];
    }

    public function parseAssignVariable($tokens, $idx, $endIdx, $hitToken, $content, $line)
    {
        $varName = is_array($tokens[$idx]) ? $tokens[$idx][1] : $tokens[$idx];
        $assign = null;

        for ($i = $idx + 1; $i < $endIdx; ++$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            if (empty($assign) && '' === trim($data)) {
                continue;
            }
            if (null === $assign && '=' === $data) {
                $assign = '';
                continue;
            }
            $name = is_array($token) ? $token[0] : null;
            if (T_VARIABLE == $name) {
                if ($data === $varName) {
                    throw new \Exception("Can't assign $data to $varName");
                }

                return $this->parseCallOnVariable($tokens, $i, $hitToken, $content, $line);
            }
            break;
        }

        return [false, null, null, null];
    }

    public function parseAssignNewClass($tokens, $idx, $endIdx, $hitToken, $content, $line)
    {
        $caller = null;
        for ($i = $idx + 1; $i < $endIdx; ++$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;
            if (empty($caller) && '' === trim($data)) {
                continue;
            }
            if (null === $caller && '=' === $data) {
                $caller = '';
                continue;
            }
            if ('(' == $data && '' === $caller) {
                continue;
            }
            $name = is_array($token) ? $token[0] : null;
            if (T_NEW == $name) {
                continue;
            }
            if ('\\' == $data || preg_match('/^[A-Za-z_]+[A-Za-z0-9_]*$/', $data)) {
                $caller = $caller.$data;
            } else {
                break;
            }
        }

        if (! $caller) {
            return [false, null, null, null];
        }

        $method = is_array($hitToken) ? $hitToken[1] : $hitToken;
        $nsParser = new \PhpCTags\Parser\Namespace_();

        return $nsParser->parseMethod($method, $caller, $content, $line);
    }
}
