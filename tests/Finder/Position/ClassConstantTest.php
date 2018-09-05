<?php

namespace Tests\Finder\Position;

class ClassConstantTest extends \Tests\BaseTest
{
    public function testFindClassConstant()
    {
        $root = realpath(__DIR__.'/../../data');

        $file = $root.'/class/constant.php';
        require_once $file;

        $cases = [
            [
                'input' => [$root, $file, 'BAZ', 'Bar', 'Foo'],
                'output' => new \PhpCTags\Position(
                    $file,
                    7,
                    11
                ),
            ],
        ];

        foreach ($cases as $i => $case) {
            $finder = new \PhpCTags\Finder\Position\ClassConstant();
            $finder->file = $case['input'][1];
            $finder->name = $case['input'][2];
            $finder->class = $case['input'][3];
            $finder->namespace = $case['input'][4];
            $finder->autoload = 'vendor/autoload.php';

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
