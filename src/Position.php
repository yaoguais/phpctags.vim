<?php

namespace PhpCTags;

class Position
{
    public $file;
    public $line;
    public $column;

    public function __construct($file, $line, $column)
    {
        $this->file = $file;
        $this->line = $line;
        $this->column = $column;
    }
}
