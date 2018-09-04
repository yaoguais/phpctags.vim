<?php

namespace Tests;

class BaseTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $options = getopt('v', ['verbose']);
        if (isset($options['v']) || isset($options['verbose'])) {
            \PhpCTags\Logger::getInstance()->setWriter(fopen('php://stdout', 'w'));
        }
    }

    public function testDummy()
    {
        $this->assertTrue(true);
    }
}
