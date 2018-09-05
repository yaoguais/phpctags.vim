<?php

namespace Tests\Parser\Type;

class VariableTest extends \Tests\BaseTest
{
    public function testParseVariableInMain()
    {
        $code = '<?php
$foo = null;
$bar = $foo;
$baz = $bar;
';

        $cases = [
            [[$code, 2, 2], [true, T_VARIABLE, '$foo']],
            [[$code, 9, 3], [true, T_VARIABLE, '$bar']],
            [[$code, 16, 4], [true, T_VARIABLE, '$baz']],
        ];

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);

            try {
                $parser = new \PhpCTags\Parser\Type\Variable();
                $result = $parser->parse($tokens, $case[0][1], $case[0][0], $case[0][2]);
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i");
                continue;
            }
            $this->assertEquals($case[1][0], $result[0], "case #$i: ".json_encode($result));

            $token = $result[1];
            $name = is_array($token) ? $token[0] : null;
            $data = is_array($token) ? $token[1] : $token;

            $this->assertEquals($case[1][1], $name, "case #$i: token name");
            $this->assertEquals($case[1][2], $data, "case #$i: token data");
        }
    }
}
