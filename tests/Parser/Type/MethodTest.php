<?php

namespace Tests\Parser\Type;

class MethodTest extends \Tests\BaseTest
{
    public function testParsePaamayimNekudotayim()
    {
        $cases = [
            [['<?php Foo::bar();', 3, 1], [true, 'bar', 'Foo', null]],
            [['<?php Foo :: bar();', 5, 1], [true, 'bar', 'Foo', null]],
            [['<?php Foo\\Bar::baz();', 5, 1], [true, 'baz', 'Bar', 'Foo']],
            [['<?php namespace Foo; Bar::baz();', 8, 1], [true, 'baz', 'Bar', 'Foo']],
            [['<?php namespace { Foo\\Bar::baz(); }', 9, 1], [true, 'baz', 'Bar', 'Foo']],
            [['<?php namespace Foo { Bar::baz(); }', 9, 1], [true, 'baz', 'Bar', 'Foo']],
            [['<?php namespace Foo; \\Bar\\Baz::foo();', 11, 1], [true, 'foo', 'Baz', 'Bar']],
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
            [[$code, 63, 8], [true, 'bar', 'Foo', 'Bar']],
            [[$code, 71, 9], [true, 'bar', 'Foo', 'Bar']],
            [[$code, 79, 10], [true, 'bar', 'Foo', 'Bar']],
            [[$code, 103, 13], [true, 'foo', 'Baz', 'Qux']],
            [[$code, 111, 14], [true, 'foo', 'Baz', 'Qux']],
            [[$code, 119, 15], [true, 'foo', 'Baz', 'Qux']],
            [[$code, 133, 18], [true, 'foo', 'Baz', 'Qux']],
            [[$code, 141, 19], [true, 'bar', 'Baz', 'Qux']],
        ]);

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);
            try {
                $parser = new \PhpCTags\Parser\Type\Method();
                $result = $parser->parse($tokens, $case[0][1], $case[0][0], $case[0][2]);
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i");
                continue;
            }
            $this->assertEquals($case[1], $result, "case #$i: ".json_encode($result));
        }
    }

    public function testParseCallOnThis()
    {
        $code = '<?php
namespace Foo {
    class Bar {
        public function foo() {
            echo "Foo->foo()\n";
        }
        public function bar() {
            $this->foo();
            $this -> foo();
        }
    }
    class Baz extends Bar {
        public function bar() {
            parent::bar();
            $this->foo();
        }
    }
    (new Baz)->bar();
}';

        $cases = [
            [[$code, 49, 8], [true, 'foo', 'Bar', 'Foo']],
            [[$code, 59, 9], [true, 'foo', 'Bar', 'Foo']],
            [[$code, 103, 15], [true, 'foo', 'Baz', 'Foo']],
        ];

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);
            try {
                $parser = new \PhpCTags\Parser\Type\Method();
                $result = $parser->parse($tokens, $case[0][1], $case[0][0], $case[0][2]);
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i");
                continue;
            }
            $this->assertEquals($case[1], $result, "case #$i: ".json_encode($result));
        }
    }

    public function testParseCallOnVariable()
    {
        $code = '<?php
namespace Foo {
    class Bar {
        public function foo() {
            echo "Foo->foo()\n";
        }
        public function bar() {
            echo "Foo->bar()\n";
        }
    }
    class Baz extends Bar {
        public function bar() {
            echo "Baz->bar()\n";
        }
    }
    $a = new Bar();
    $b = $a;
    $c = $b;
    $b = new \\Foo\\Baz();
    $c->foo();
    $c->bar();
    
    $d = $b;
    $e = $d;
    $e->bar();
}';

        $cases = [
            [[$code, 138, 20], [true, 'foo', 'Bar', 'Foo']],
            [[$code, 146, 21], [true, 'bar', 'Bar', 'Foo']],
            [[$code, 172, 25], [true, 'bar', 'Baz', 'Foo']],
        ];

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);
            try {
                $parser = new \PhpCTags\Parser\Type\Method();
                $result = $parser->parse($tokens, $case[0][1], $case[0][0], $case[0][2]);
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i");
                continue;
            }
            $this->assertEquals($case[1], $result, "case #$i: ".json_encode($result));
        }
    }
}
