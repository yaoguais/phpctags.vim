<?php

namespace Tests\Parser;

class ClassTest extends \Tests\BaseTest
{
    public function testParseClass()
    {
        $cases = [
            ['<?php class Foo{}', [['Foo', null, T_CLASS, 1]]],
            ['<?php class Foo {}', [['Foo', null, T_CLASS, 1]]],
            ['<?php namespace Foo { class Bar {} }', [['Bar', 'Foo', T_CLASS, 1]]],
            ['<?php namespace Foo; class Bar {}', [['Bar', 'Foo', T_CLASS, 1]]],
            ['<?php namespace Foo; class Bar {}
                                   class Baz {}', [['Bar', 'Foo', T_CLASS, 1], ['Baz', 'Foo', T_CLASS, 2]]],
            ['<?php 
namespace Foo {
    class Bar{
    }
    class Baz{
    }
}
namespace Bar {
    class Foo {
    }
    class Baz
    {
    }
}          ', [
                ['Bar', 'Foo', T_CLASS, 3],
                ['Baz', 'Foo', T_CLASS, 5],
                ['Foo', 'Bar', T_CLASS, 9],
                ['Baz', 'Bar', T_CLASS, 11],
              ],
            ],
        ];

        foreach ($cases as $i => $case) {
            $parser = new \PhpCTags\Parser\Class_();
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0]);
            $this->assertEquals($case[1], $parser->parseToken($tokens), "case #$i");
        }
    }
}
