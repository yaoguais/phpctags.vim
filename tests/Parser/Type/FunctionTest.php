<?php

namespace Tests\Parser\Type;

class FunctionTest extends \Tests\BaseTest
{
    public function testParseFunction()
    {
        $cases = [
            [['<?php foo();', 0, 1], [false, null, null]],
            [['<?php foo();', 1, 1], [true, 'foo', null]],
            [['<?php foo ();', 1, 1], [true, 'foo', null]],
            [['<?php  foo();', 2, 1], [true, 'foo', null]],
            [['<?php bar();foo();', 5, 1], [true, 'foo', null]],
            [['<?php function bar(){}foo();', 8, 1], [true, 'foo', null]],
            [['<?php $a->foo();', 3, 1], [false, null, null]],
            [['<?php $a -> foo();', 5, 1], [false, null, null]],
            [['<?php new foo();', 3, 1], [false, null, null]],
            [['<?php new  foo();', 3, 1], [false, null, null]],
            [['<?php foo\bar();', 3, 1], [true, 'bar', 'foo']],
            [['<?php \foo\bar();', 4, 1], [true, 'bar', 'foo']],
        ];

        $code = '<?php
namespace Foo {
    use function Bar\\baz as Foo_baz;
    use Bar as ClassBar;
    function baz()
    {
        echo "Foo/baz\\n";
    }
    baz();
    \\Bar\\baz();
    Foo_baz();
    ClassBar\\baz();
}
namespace Bar {
    use function \\Foo\\baz as Foo_baz;
    use Foo as ClassFoo;
    function baz()
    {
        echo "Bar/baz\\n";
    }
    baz();
    \\Foo\\baz();
    Foo_baz();
    ClassFoo\\baz();
}';

        $cases = array_merge($cases, [
            [[$code, 52, 9], [true, 'baz', 'Foo']],
            [[$code, 61, 10], [true, 'baz', 'Bar']],
            [[$code, 67, 11], [true, 'baz', 'Bar']],
            [[$code, 75, 12], [true, 'baz', 'Bar']],
            [[$code, 133, 21], [true, 'baz', 'Bar']],
            [[$code, 142, 22], [true, 'baz', 'Foo']],
            [[$code, 148, 23], [true, 'baz', 'Foo']],
            [[$code, 156, 24], [true, 'baz', 'Foo']],
        ]);

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);

            try {
                $parser = new \PhpCTags\Parser\Type\Function_();
                $result = $parser->parse($tokens, $case[0][1], $case[0][0], $case[0][2]);
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i");
                continue;
            }
            $this->assertEquals($case[1], $result, "case #$i: ".json_encode($result));
        }
    }
}
