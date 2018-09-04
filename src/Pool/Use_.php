<?php

namespace PhpCTags\Pool;

class Use_
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
            $parser = new \PhpCTags\Parser\Use_();
            $nodes = Node::getInstance()->fromContent($content);
            $this->caches[$key] = $parser->parse($nodes);
        }

        return $this->caches[$key];
    }
}
