<?php

namespace PhpCTags\Pool;

class Token
{
    use \PhpCTags\Singleton;

    protected $caches = [];

    public function fromFile($file)
    {
        return $this->fromContent(File::getInstance()->fromFile($file));
    }

    public function fromContent($content)
    {
        $key = md5($content);
        if (! array_key_exists($key, $this->caches)) {
            $parser = new \PhpCTags\Parser\Token();
            $this->caches[$key] = $parser->parse($content);
        }

        return $this->caches[$key];
    }
}
