<?php

namespace Tests\Parser;

class UseTest extends \Tests\BaseTest
{
    public function testParse()
    {
        $cases = [
            [
                '<?php 
use A\B\ClassC;',
                [
                    'classc' => [['A\B\ClassC', \PhpParser\Node\Stmt\Use_::TYPE_NORMAL, 2]],
                ],
            ],
            [
                '<?php
namespace A {
    function foo() {
    }
    function bar() {
    }
}
namespace A\B {
    use function A\foo;
}
namespace A\C {
    use function A\bar as foo;
}',
                [
                    'foo' => [
                        ['A\foo', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 9],
                        ['A\bar', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 12],
                    ],
                ],
            ],
            [
                '<?php 
use function foo\math\sin, foo\math\cos, foo\math\cosh;
use const foo\math\PI, foo\math\E, foo\math\GAMMA, foo\math\GOLDEN_RATIO;
use function foo\math\{ sin2 as sin2as, cos2, cosh2 };
use const foo\math\{ PI2, E2, GAMMA2, GOLDEN_RATIO2 };
use bar\math\Math3;
use const bar\math\PI3;
use function bar\math\sin3, bar\math\cos3, bar\math\cosh3;
use baz\math\{ Math4, const PI4, function sin4, function cos4, function cosh4 };',
                [
                    'sin' => [['foo\math\sin', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 2]],
                    'cos' => [['foo\math\cos', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 2]],
                    'cosh' => [['foo\math\cosh', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 2]],
                    'pi' => [['foo\math\PI', \PhpParser\Node\Stmt\Use_::TYPE_CONSTANT, 3]],
                    'e' => [['foo\math\E', \PhpParser\Node\Stmt\Use_::TYPE_CONSTANT, 3]],
                    'gamma' => [['foo\math\GAMMA', \PhpParser\Node\Stmt\Use_::TYPE_CONSTANT, 3]],
                    'golden_ratio' => [['foo\math\GOLDEN_RATIO', \PhpParser\Node\Stmt\Use_::TYPE_CONSTANT, 3]],
                    'sin2as' => [['foo\math\sin2', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 4]],
                    'cos2' => [['foo\math\cos2', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 4]],
                    'cosh2' => [['foo\math\cosh2', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 4]],
                    'pi2' => [['foo\math\PI2', \PhpParser\Node\Stmt\Use_::TYPE_CONSTANT, 5]],
                    'e2' => [['foo\math\E2', \PhpParser\Node\Stmt\Use_::TYPE_CONSTANT, 5]],
                    'gamma2' => [['foo\math\GAMMA2', \PhpParser\Node\Stmt\Use_::TYPE_CONSTANT, 5]],
                    'golden_ratio2' => [['foo\math\GOLDEN_RATIO2', \PhpParser\Node\Stmt\Use_::TYPE_CONSTANT, 5]],
                    'math3' => [['bar\math\Math3', \PhpParser\Node\Stmt\Use_::TYPE_NORMAL, 6]],
                    'pi3' => [['bar\math\PI3', \PhpParser\Node\Stmt\Use_::TYPE_CONSTANT, 7]],
                    'sin3' => [['bar\math\sin3', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 8]],
                    'cos3' => [['bar\math\cos3', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 8]],
                    'cosh3' => [['bar\math\cosh3', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 8]],
                    'math4' => [['baz\math\Math4', \PhpParser\Node\Stmt\Use_::TYPE_NORMAL, 9]],
                    'pi4' => [['baz\math\PI4', \PhpParser\Node\Stmt\Use_::TYPE_CONSTANT, 9]],
                    'sin4' => [['baz\math\sin4', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 9]],
                    'cos4' => [['baz\math\cos4', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 9]],
                    'cosh4' => [['baz\math\cosh4', \PhpParser\Node\Stmt\Use_::TYPE_FUNCTION, 9]],
                ],
            ],
        ];
        foreach ($cases as $i => $case) {
            $parser = new \PhpCTags\Parser\Use_();
            $nodeParser = new \PhpCTags\Parser\Node();
            $this->assertEquals(
                $case[1],
                $parser->parse($nodeParser->parse($case[0])),
                "case #$i"
            );
        }
    }
}
