<?php

namespace Tests\Parser;

class ClassTest extends \Tests\BaseTest
{
    public function testParseClass()
    {
        $cases = [
            ['<?php class Foo{}', [['Foo', null, 1]]],
            ['<?php class Foo {}', [['Foo', null, 1]]],
            ['<?php namespace Foo { class Bar {} }', [['Bar', 'Foo', 1]]],
            ['<?php namespace Foo; class Bar {}', [['Bar', 'Foo', 1]]],
            ['<?php namespace Foo; class Bar {}
                                   class Baz {}', [['Bar', 'Foo', 1], ['Baz', 'Foo', 2]]],
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
                ['Bar', 'Foo', 3],
                ['Baz', 'Foo', 5],
                ['Foo', 'Bar', 9],
                ['Baz', 'Bar', 11],
              ],
            ],
        ];

        foreach ($cases as $i => $case) {
            $parser = new \PhpCTags\Parser\Class_();
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0]);
            $this->assertEquals($case[1], $parser->parse($tokens), "case #$i");
        }
    }
}
