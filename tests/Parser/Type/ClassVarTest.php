<?php

namespace Tests\Parser\Type;

class ClassVarTest extends \Tests\BaseTest
{
    public function testParseClassVar()
    {
        $code = '<?php
namespace {
    class Foo {
        public $foo;
        function foo() {
            echo $this->foo;
        }
    }
}
namespace Foo {
    class Bar {
        public $bar;
        function bar() {
            echo $this -> bar ;
        }
    }
}';

        $cases = [
            [[$code, 33, 6], [true, 'foo', 'Foo', null]],
            [[$code, 79, 14], [true, 'bar', 'Bar', 'Foo']],
        ];

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);
            try {
                $parser = new \PhpCTags\Parser\Type\ClassVar();
                $result = $parser->parse($tokens, $case[0][1], $case[0][0], $case[0][2]);
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i");
                continue;
            }
            $this->assertEquals($case[1], $result, "case #$i: ".json_encode($result));
        }
    }
}
