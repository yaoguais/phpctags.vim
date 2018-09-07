<?php

namespace PhpCTags\Finder\Position;

abstract class BaseFinder
{
    protected function throwException($message = null, $code = 0)
    {
        $parts = explode('\\', get_class($this));
        $name = array_pop($parts);
        $name = rtrim($name, '_');
        $message = $name.' Finder '.$message;

        throw new \Exception($message, $code);
    }
}
