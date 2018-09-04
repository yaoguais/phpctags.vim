<?php

namespace Tests\Parser;

class TypeTest extends \Tests\BaseTest
{
    public function testIndex()
    {
        $cases = [
            [['<?php ', 1, 1, 'php'], -1],
            [['<?php foo();', 1, 6, 'foo'], -1],
            [['<?php foo();', 1, 7, 'foo'], 1],
            [['<?php foo();', 1, 8, 'foo'], 1],
            [['<?php foo();', 1, 9, 'foo'], 1],
            [['<?php foo();', 1, 10, 'foo'], -1],
            [['<?php
foo();', 2, 1, 'foo'], 2],
            [['<?php
foo();foo();', 2, 6, 'foo'], -1],
            [['<?php
foo();foo();', 2, 7, 'foo'], 6],
            [['<?php
foo();foo();', 2, 8, 'foo'], 6],
            [['<?php
foo();foo();', 2, 9, 'foo'], 6],
            [['<?php
foo();foo();', 2, 10, 'foo'], -1],
        ];

        foreach ($cases as $i => $case) {
            $tokenParser = new \PhpCTags\Parser\Token();
            $tokens = $tokenParser->parse($case[0][0]);

            $parser = new \PhpCTags\Parser\Type_();
            $this->assertEquals(
                $case[1],
                $parser->index($tokens, $case[0][1], $case[0][2], $case[0][3]),
                "case #$i"
            );
        }
    }
}
