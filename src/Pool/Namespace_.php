<?php

namespace PhpCTags\Pool;

class Namespace_
{
    use \PhpCTags\Singleton;

    protected $caches = [];

    public function fromContent($content)
    {
        $key = md5($content);
        if (! array_key_exists($key, $this->caches)) {
            $parser = new \PhpCTags\Parser\Namespace_();
            $tokens = Token::getInstance()->fromContent($content);
            $this->caches[$key] = $parser->parseToken($tokens);
        }

        return $this->caches[$key];
    }
}
