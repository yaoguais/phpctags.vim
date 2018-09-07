<?php

namespace Tests\Parser\Type;

class ClassStaticVarTest extends \Tests\BaseTest
{
    public function testParseClassStaticVar()
    {
        $code = '<?php
namespace {
    class Foo {
        static $foo;
        function foo() {
            echo self::$foo;
        }
    }
}
namespace Foo {
    class Bar {
        static $bar;
        function bar() {
            echo static :: $bar ;
        }
    }
}';

        $cases = [
            [[$code, 33, 6], [true, '$foo', 'Foo', null]],
            [[$code, 79, 14], [true, '$bar', 'Bar', 'Foo']],
        ];

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);
            try {
                $parser = new \PhpCTags\Parser\Type\ClassStaticVar();
                $result = $parser->parse($tokens, $case[0][1], $case[0][0], $case[0][2]);
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i");
                continue;
            }
            $this->assertEquals($case[1], $result, "case #$i: ".json_encode($result));
        }
    }
}
