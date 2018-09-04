<?php

namespace PhpCTags;

/**
 * Class Logger.
 *
 * @method debug(string $message, $context = [])
 * @method info(string $message, $context = [])
 * @method warning(string $message, $context = [])
 * @method error(string $message, $context = [])
 */
class Logger
{
    use Singleton;

    public $writer;

    public function setWriter($writer)
    {
        $this->writer = $writer;
    }

    public function __call($name, $arguments)
    {
        if (is_resource($this->writer)) {
            $message = sprintf("[%s] %s\n", strtoupper($name), json_encode($arguments));
            fputs($this->writer, $message);
        }
    }

    public static function __callStatic($name, $arguments)
    {
        self::getInstance()->__call($name, $arguments);
    }
}
