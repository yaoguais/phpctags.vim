<?php

namespace PhpCTags\Parser;

class Root
{
    public function parse($file, $autoload)
    {
        $root = dirname($file);
        $dir = $root;
        $isWindows = 'WIN' === strtoupper(substr(PHP_OS, 0, 3));
        do {
            if (file_exists($dir.DIRECTORY_SEPARATOR.$autoload)) {
                $root = $dir;
                break;
            }
            $check = $isWindows ? substr($dir, 1 + strpos($dir, ':')) : $dir;
            if (strlen($check) <= 1) {
                break;
            }
            $dir = dirname($dir);
        } while (true);

        return $root;
    }
}
