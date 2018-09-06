<?php

namespace Tests\Finder\Position;

class ClassTest extends \Tests\BaseTest
{
    public function testFindClass()
    {
        $root = realpath(__DIR__.'/../../data');

        $file = $root.'/class/class.php';
        require_once $file;

        $cases = [
            [
                'input' => [$root, $file, 'Foo', 'Baz'],
                'output' => new \PhpCTags\Position(
                    $file,
                    5,
                    7
                ),
            ],
            [
                'input' => [$root, $file, 'Bar', 'Baz'],
                'output' => new \PhpCTags\Position(
                    $file,
                    20,
                    7
                ),
            ],
            [
                'input' => [$root, $file, 'Baz', 'Baz'],
                'output' => new \PhpCTags\Position(
                    $file,
                    10,
                    11
                ),
            ],
            [
                'input' => [$root, $file, 'Qux', 'Baz'],
                'output' => new \PhpCTags\Position(
                    $file,
                    15,
                    11
                ),
            ],
        ];

        foreach ($cases as $i => $case) {
            $finder = new \PhpCTags\Finder\Position\Class_();
            $finder->file = $case['input'][1];
            $finder->name = $case['input'][2];
            $finder->namespace = $case['input'][3];
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
