<?php

namespace Tests\Parser\Type;

class ConstTest extends \Tests\BaseTest
{
    public function testParseConst()
    {
        $cases = [
            [['<?php echo Foo;', 3, 1], [true, 'Foo', null]],
            [['<?php echo Foo\\Bar;', 3, 1], [true, 'Bar', 'Foo']],
            [['<?php echo Foo\\Bar;', 4, 1], [false, null, null]],
            [['<?php echo Foo\\Bar;', 5, 1], [true, 'Bar', 'Foo']],
        ];

        $code = '<?php
namespace Foo;
const BAR = 1;
echo BAR;
echo \\Foo\\BAR;';

        $cases = array_merge($cases, [
            [[$code, 18, 4], [true, 'BAR', 'Foo']],
            [[$code, 24, 5], [true, 'BAR', 'Foo']],
            [[$code, 26, 5], [true, 'BAR', 'Foo']],
        ]);

        $code = '<?php
namespace Foo {
    const BAR = "BAR";
}
namespace Baz {
    use const Foo\\BAR;
    use const Foo\\BAR as FooBAR;
    echo BAR;
    echo FooBAR;
    echo \\Foo\\BAR;
}';

        $cases = array_merge($cases, [
            [[$code, 53, 8], [true, 'BAR', 'Foo']],
            [[$code, 59, 9], [true, 'BAR', 'Foo']],
            [[$code, 66, 10], [true, 'BAR', 'Foo']],
            [[$code, 68, 10], [true, 'BAR', 'Foo']],
        ]);

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);

            try {
                $parser = new \PhpCTags\Parser\Type\Const_();
                $result = $parser->parse($tokens, $case[0][1], $case[0][0], $case[0][2]);
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i");
                continue;
            }
            $this->assertEquals($case[1], $result, "case #$i: ".json_encode($result));
        }
    }
}
