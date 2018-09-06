<?php

namespace Tests\Parser\Type;

class ClassTest extends \Tests\BaseTest
{
    public function testParseClassWithNew()
    {
        $code = '<?php
namespace {
    (new \\Foo\\Bar)->bar();
}
namespace Foo {
    class Bar {
        function bar(){}
    }
    class Baz extends Bar {
    }
    (new Baz)->bar();
    (new \\Foo\\Bar)->bar();
}';

        $cases = [
            [[$code, 11, 3], [true, 'Bar', 'Foo']],
            [[$code, 13, 3], [true, 'Bar', 'Foo']],
            [[$code, 66, 11], [true, 'Baz', 'Foo']],
            [[$code, 79, 12], [true, 'Bar', 'Foo']],
            [[$code, 81, 12], [true, 'Bar', 'Foo']],
        ];

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);

            try {
                $parser = new \PhpCTags\Parser\Type\Class_();
                $result = $parser->parse($tokens, $case[0][1], $case[0][0], $case[0][2]);
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i");
                continue;
            }
            $this->assertEquals($case[1], $result, "case #$i: ".json_encode($result));
        }
    }

    public function testParseClassWithPaamayimNekudotayim()
    {
        $cases = [
            [['<?php Foo::bar();', 1, 1], [true, 'Foo', null]],
            [['<?php Foo :: bar();', 1, 1], [true, 'Foo', null]],
            [['<?php Foo\\Bar::baz();', 1, 1], [true, 'Bar', 'Foo']],
            [['<?php Foo\\Bar::baz();', 3, 1], [true, 'Bar', 'Foo']],
            [['<?php namespace Foo; Bar::baz();', 6, 1], [true, 'Bar', 'Foo']],
            [['<?php namespace { Foo\\Bar::baz(); }', 5, 1], [true, 'Bar', 'Foo']],
            [['<?php namespace { Foo\\Bar::baz(); }', 7, 1], [true, 'Bar', 'Foo']],
            [['<?php namespace Foo { Bar::baz(); }', 7, 1], [true, 'Bar', 'Foo']],
            [['<?php namespace Foo; \\Bar\\Baz::foo();', 7, 1], [true, 'Baz', 'Bar']],
            [['<?php namespace Foo; \\Bar\\Baz::foo();', 9, 1], [true, 'Baz', 'Bar']],
        ];

        $code = '<?php
namespace Qux {
    use Bar;
    use Bar\\Foo;
    use Bar\\Foo as BarFoo;
    class Baz extends Foo {
        public static function foo() {
            Bar\\Foo::bar();
            Foo::bar();
            BarFoo::bar();
        }
        public static function bar() {
            self::foo();
            static::foo();
            parent::foo();
        }
    }
    Baz::foo();
    Baz::bar();
}
namespace Bar {
    class Foo {
        public static function foo() {
            echo "Bar\\Foo::foo()\n";
        }
        public static function bar() {
            echo "Bar\\Foo::bar()\n";
        }
    }
}';
        $cases = array_merge($cases, [
            [[$code, 61, 8], [true, 'Foo', 'Bar']],
            [[$code, 69, 9], [true, 'Foo', 'Bar']],
            [[$code, 77, 10], [true, 'Foo', 'Bar']],
            [[$code, 101, 13], [true, 'Baz', 'Qux']],
            [[$code, 109, 14], [true, 'Baz', 'Qux']],
            [[$code, 117, 15], [true, 'Baz', 'Qux']],
            [[$code, 131, 18], [true, 'Baz', 'Qux']],
            [[$code, 139, 19], [true, 'Baz', 'Qux']],
        ]);

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);

            try {
                $parser = new \PhpCTags\Parser\Type\Class_();
                $result = $parser->parse($tokens, $case[0][1], $case[0][0], $case[0][2]);
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i");
                continue;
            }
            $this->assertEquals($case[1], $result, "case #$i: ".json_encode($result));
        }
    }
}
