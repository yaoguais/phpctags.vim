<?php

namespace Tests\Parser\Type;

class ClassConstTest extends \Tests\BaseTest
{
    public function testParseClassConst()
    {
        $cases = [
            [['<?php Foo::BAR;', 3, 1], [true, 'BAR', 'Foo', null]],
            [['<?php Foo :: BAR;', 5, 1], [true, 'BAR', 'Foo', null]],
            [['<?php Foo\\Bar::BAZ;', 5, 1], [true, 'BAZ', 'Bar', 'Foo']],
            [['<?php namespace Foo; Bar::BAZ;', 8, 1], [true, 'BAZ', 'Bar', 'Foo']],
            [['<?php namespace { Foo\\Bar::BAZ; }', 9, 1], [true, 'BAZ', 'Bar', 'Foo']],
            [['<?php namespace Foo { Bar::BAZ; }', 9, 1], [true, 'BAZ', 'Bar', 'Foo']],
            [['<?php namespace Foo; \\Bar\\Baz::FOO;', 11, 1], [true, 'FOO', 'Baz', 'Bar']],
        ];

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);
            try {
                $parser = new \PhpCTags\Parser\Type\ClassConst();
                $result = $parser->parse($tokens, $case[0][1], $case[0][0], $case[0][2]);
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i");
                continue;
            }
            $this->assertEquals($case[1], $result, "case #$i: ".json_encode($result));
        }
    }
}
