<?php

namespace Tests\Finder;

class NamespaceTest extends \Tests\BaseTest
{
    public function testParse()
    {
        $cases = [
            ['<?php namespace {}', []],
            ['<?php namespace Foo;', [['Foo', 1]]],
            ['<?php namespace Foo ;', [['Foo', 1]]],
            ['<?php namespace Foo{}', [['Foo', 1]]],
            ['<?php namespace Foo {}', [['Foo', 1]]],
            ['<?php namespace Foo\\Bar;', [['Foo\\Bar', 1]]],
            ['<?php namespace Foo\\Bar{}', [['Foo\\Bar', 1]]],
            [
                '<?php
                
                namespace Foo\\Bar{}',
                [['Foo\\Bar', 3]],
            ],
            [
                '<?php namespace {
            
                }
                namespace Foo{
                
                }
                namespace Foo\\Bar{
                
                }',
                [
                    ['Foo', 4],
                    ['Foo\\Bar', 7],
                ],
            ],
        ];

        foreach ($cases as $i => $case) {
            $parser = new \PhpCTags\Parser\Namespace_();
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0]);
            $this->assertEquals($case[1], $parser->parseToken($tokens), "case #$i");
        }
    }
}
