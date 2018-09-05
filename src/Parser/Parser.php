<?php

namespace PhpCtags\Parser;

interface Parser
{
    public function parse($tokens, $idx, $content, $line);
}
