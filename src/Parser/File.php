<?php

namespace PhpCTags\Parser;

class File
{
    public function parse($file)
    {
        if (! file_exists($file)) {
            throw new \Exception("file is not exist: $file");
        }
        if (! is_readable($file)) {
            throw new \Exception("file is not readable: $file");
        }

        return file_get_contents($file);
    }
}
