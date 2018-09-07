<?php

namespace Tests\Finder\Position;

class ClassConstTest extends \Tests\BaseTest
{
    public function testFindClassConst()
    {
        $root = realpath(__DIR__.'/../../data');

        $file = $root.'/class/constant.php';
        require_once $file;

        $cases = [
            [
                'input' => ['BAZ', 'Bar', 'Foo'],
                'output' => new \PhpCTags\Position(
                    $file,
                    7,
                    11
                ),
            ],
            [
                'input' => ['BAZ', 'Baz', 'Foo'],
                'output' => new \PhpCTags\Position(
                    $file,
                    7,
                    11
                ),
            ],
            [
                'input' => ['FOO', 'Baz', 'Foo'],
                'output' => new \PhpCTags\Position(
                    $file,
                    22,
                    11
                ),
            ],
            [
                'input' => ['BAR', 'Baz', 'Foo'],
                'output' => new \PhpCTags\Position(
                    $file,
                    27,
                    11
                ),
            ],
        ];

        foreach ($cases as $i => $case) {
            $finder = new \PhpCTags\Finder\Position\ClassConst();
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
