<?php

namespace PhpCTags\Finder\Position;

class Variable extends BaseFinder implements Finder
{
    public $file;
    public $tokens;
    public $index;

    public function validate()
    {
        if (! $this->file) {
            $this->throwException('file is invalid');
        }
        if (! $this->tokens) {
            $this->throwException('tokens is invalid');
        }
        if (! array_key_exists($this->index, $this->tokens)) {
            $this->throwException('index is invalid');
        }
        $token = $this->tokens[$this->index];
        if (! is_array($token) || T_VARIABLE != $token[0]) {
            $this->throwException('token is not a variable type');
        }
    }

    public function find()
    {
        $this->validate();

        $positions = [];
        $varName = $this->tokens[$this->index][1];
        $idx = $this->index;
        do {
            $ret = $this->parse($this->tokens, $idx, $varName);
            if (! $ret) {
                break;
            }
            $idx = $ret[2];
            $position = new \PhpCTags\Position($this->file, $ret[0], $ret[1]);
            array_unshift($positions, $position);
        } while (true);

        if (0 == count($positions)) {
            $this->throwException('no target variable not found');
        }

        return $positions[0];
    }

    public function parse($tokens, $idx, $varName)
    {
        $target = null;
        for ($i = $idx - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;

            if (T_VARIABLE == $name && $data === $varName) {
                $target = $token;
                break;
            }
            if (T_FUNCTION == $name) {
                break;
            }
        }

        if (is_null($target)) {
            return null;
        }

        $targetIndex = $i;
        $sameLine = true;
        $column = 1;
        $line = 1;
        for ($i = $i - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            $data = is_array($token) ? $token[1] : $token;

            if (\PhpCTags\Parser\Token::isNewLine($token)) {
                $sameLine = false;
                ++$line;
            }
            if ($sameLine) {
                $column += strlen($data);
            }
        }

        return [$line, $column, $targetIndex];
    }
}
