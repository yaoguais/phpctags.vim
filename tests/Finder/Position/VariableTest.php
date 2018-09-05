<?php

namespace Tests\Finder\Position;

class VariableTest extends \Tests\BaseTest
{
    public function testFindVariable()
    {
        $root = realpath(__DIR__.'/../../data');
        $cases = [
            [
                // $foo = null
                [$root.'/variable/main.php', 4],
                'no target variable not found',
            ],
            [
                // $bar = $foo;
                [$root.'/variable/main.php', 16],
                new \PhpCTags\Position($root.'/variable/main.php', 3, 3),
            ],
            [
                // $baz = $bar;
                [$root.'/variable/main.php', 24],
                new \PhpCTags\Position($root.'/variable/main.php', 4, 3),
            ],
        ];

        foreach ($cases as $i => $case) {
            $finder = new \PhpCTags\Finder\Position\Variable();
            $finder->file = $case[0][0];
            $parser = new \PhpCTags\Parser\Token();
            $finder->tokens = $parser->parse(file_get_contents($case[0][0]));
            $finder->index = $case[0][1];

            try {
                $position = $finder->find();
            } catch (\Exception $e) {
                $this->assertEquals($case[1], $e->getMessage(), "case #$i throw exception");
                continue;
            }

            $this->assertEquals($case[1]->file, $position->file, "case #$i check file");
            $this->assertEquals($case[1]->line, $position->line, "case #$i check line");
            $this->assertEquals($case[1]->column, $position->column, "case #$i check column");
        }
    }
}
