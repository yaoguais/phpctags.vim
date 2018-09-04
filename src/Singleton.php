<?php

namespace PhpCTags;

trait Singleton
{
    protected static $instance;

    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __sleep()
    {
    }

    private function __wakeup()
    {
    }
}
