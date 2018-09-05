<?php

namespace PhpCTags\Parser\Type;

interface Parser
{
    public function parse($tokens, $idx, $content, $line);
}
