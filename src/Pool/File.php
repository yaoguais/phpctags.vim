<?php

namespace PhpCTags\Pool;

class File
{
    use \PhpCTags\Singleton;

    protected $caches = [];

    public function fromFile($file)
    {
        $file = realpath($file);
        if (! array_key_exists($file, $this->caches)) {
            $this->caches[$file] = file_get_contents($file);
        }

        return $this->caches[$file];
    }
}
