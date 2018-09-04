<?php

namespace PhpCTags\Pool;

class Class_
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
            $parser = new \PhpCTags\Parser\Class_();
            $tokens = Token::getInstance()->fromContent($content);
            $this->caches[$key] = $parser->parse($tokens);
        }

        return $this->caches[$key];
    }
}
