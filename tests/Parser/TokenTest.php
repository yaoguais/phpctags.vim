<?php

namespace Tests\Parser;

class TokenTest extends \Tests\BaseTest
{
    public function testParse()
    {
        $source0 = <<<EOF
<?php
\$a = null;
EOF;
        $source1 = <<<EOF
<?php
\$foo = "foo\nbar";
EOF;
        $source2 = <<<EOF
<?php
/*foo
bar
baz*/
EOF;
        $source3 = '<?php
  /// foo\nbar\nbaz
';
        $source4 = '<?php
  # foo\nbar\nbaz
';

        // for loading undefined constant T_NEW_LINE
        new \PhpCTags\Parser\Token();

        $cases = [
            [$source0, 2,
                [
                    T_OPEN_TAG, T_NEW_LINE,
                    T_VARIABLE, T_WHITESPACE, '=', T_WHITESPACE, T_STRING, ';',
                ],
            ],
            [$source1, 2,
                [
                    T_OPEN_TAG, T_NEW_LINE,
                    T_VARIABLE, T_WHITESPACE, '=', T_WHITESPACE, T_CONSTANT_ENCAPSED_STRING, ';',
                ],
            ],
            [$source2, 4,
                [
                    T_OPEN_TAG, T_NEW_LINE,
                    T_COMMENT, T_NEW_LINE,
                    T_COMMENT, T_NEW_LINE,
                    T_COMMENT,
                ],
            ],
            [$source3, 3,
                [
                    T_OPEN_TAG, T_NEW_LINE,
                    T_WHITESPACE, T_COMMENT, T_NEW_LINE,
                ],
            ],
            [$source4, 3,
                [
                    T_OPEN_TAG, T_NEW_LINE,
                    T_WHITESPACE, T_COMMENT, T_NEW_LINE,
                ],
            ],
        ];

        foreach ($cases as $i => $case) {
            $parser = new \PhpCTags\Parser\Token();
            $tokens = $parser->parse($case[0]);
            $list = [];
            $line = [];
            foreach ($tokens as $token) {
                $name = is_array($token) ? $token[0] : null;
                $data = is_array($token) ? $token[1] : $token;
                if (\PhpCTags\Parser\Token::isNewLine($token)) {
                    $line[] = $token;
                }
                $list[] = $name ? $name : $data;
            }
            $this->assertEquals($case[2], $list, "case #$i: tokens \n".var_export($tokens, true));
            $this->assertEquals($case[1], count($line) + 1, "case #$i: line count");
        }
    }

    public function testParseRange()
    {
        $cases = [];

        $code = '<?php
namespace Foo;
# constant BAR {
const BAR = 0;
// constant BAZ {
define("BAZ", "BAZ");
/** class Foo { */
class Foo {
    const FOO = "FOO";
    function foo() {
        return "{}";
    }
    function bar() {
        $baz = new class {
            const BAZ = "BAZ";
            function baz() {
                $qux = new class {
                    function qux() {}
                    const QUX = "QUX";
                };
            }
        };
    }
}';

        $cases = array_merge($cases, [
            [$code, [
                [T_CLASS, 31, 149, 8, 24],
                [T_CLASS, 81, 143, 14, 22],
                [T_CLASS, 111, 136, 17, 20],
            ]],
        ]);

        foreach ($cases as $i => $case) {
            $parser = new \PhpCTags\Parser\Token();
            $tokens = $parser->parse($case[0]);
            $ranges = $parser->parseRange($tokens, [T_CLASS, T_TRAIT, T_INTERFACE]);
            $this->assertEquals($case[1], $ranges, "case #$i");
        }
    }
}
