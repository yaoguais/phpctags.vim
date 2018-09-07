<?php

namespace Tests\Finder\Position;

class ClassVarTest extends \Tests\BaseTest
{
    public function testFindClassVar()
    {
        $root = realpath(__DIR__.'/../../data');

        $file = $root.'/class/var.php';
        require_once $file;

        $cases = [
            [
                'input' => ['foo', 'Qux', 'Qux'],
                'output' => new \PhpCTags\Position(
                    $file,
                    7,
                    12
                ),
            ],
            [
                'input' => ['bar', 'Qux', 'Qux'],
                'output' => new \PhpCTags\Position(
                    $file,
                    8,
                    15
                ),
            ],
            [
                'input' => ['baz', 'Qux', 'Qux'],
                'output' => new \PhpCTags\Position(
                    $file,
                    9,
                    13
                ),
            ],
        ];

        foreach ($cases as $i => $case) {
            $finder = new \PhpCTags\Finder\Position\ClassVar();
            $finder->name = $case['input'][0];
            $finder->class = $case['input'][1];
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
