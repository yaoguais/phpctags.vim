<?php

namespace Tests\Finder\Position;

class ConstantTest extends \Tests\BaseTest
{
    public function testFindFromFunctionDefine()
    {
        $root = realpath(__DIR__.'/../../data');
        $cases = [
            [
                'input' => [$root, 'FOO', null],
                'output' => new \PhpCTags\Position(
                    $root.'/constant/define.php',
                    3,
                    9
                ),
            ],
            [
                'input' => [$root, 'BAR', 'Foo'],
                'output' => new \PhpCTags\Position(
                    $root.'/constant/define.php',
                    4,
                    13
                ),
            ],
            [
                'input' => [$root, 'BAZ', 'Foo\Bar'],
                'output' => new \PhpCTags\Position(
                    $root.'/constant/define.php',
                    5,
                    19
                ),
            ],
        ];

        foreach ($cases as $i => $case) {
            $finder = new \PhpCTags\Finder\Position\Constant_();
            $finder->root = $case['input'][0];
            $finder->name = $case['input'][1];
            $finder->namespace = $case['input'][2];

            try {
                $position = $finder->find();
            } catch (\Exception $e) {
                $this->assertEquals($case['output'], $e->getMessage(), "case #$i throw exception");
                continue;
            }

            $this->assertEquals($case['output']->file, $position->file, "case #$i check file");
            $this->assertEquals($case['output']->line, $position->line, "case #$i check line");
            $this->assertEquals($case['output']->column, $position->column, "case #$i check column");
        }
    }
}
