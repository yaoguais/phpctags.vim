<?php

namespace Tests\Finder\Position;

class FunctionTest extends \Tests\BaseTest
{
    public function testMatchClass()
    {
        $cases = [
            ['<?php class Foo {}', true],
            ['<?php class Foo extends Bar {}', true],
            ['<?php class Foo extends Bar\\Baz {}', true],
            ['<?php class Foo implements Bar{}', true],
            ['<?php class Foo implements Bar, Baz{}', true],
            ['<?php class Foo extends Bar implements Baz{}', true],
            ['<?php class Foo extends Bar implements Bar, Baz{}', true],
        ];
        foreach ($cases as $i => $case) {
            $this->assertEquals($case[1],
                preg_match(\PhpCTags\Finder\Position\Function_::CLASS_PATTERN, $case[0]),
                "case #$i"
            );
        }
    }

    public function testFindNoNamespace()
    {
        $root = realpath(__DIR__.'/../../data');
        $cases = [
            [
                'input' => [$root, 'foo', null],
                'output' => new \PhpCTags\Position(
                    $root.'/function/no_namespace.php',
                    3,
                    10
                ),
            ],
            [
                'input' => [$root, 'bar', null],
                'output' => new \PhpCTags\Position(
                    $root.'/function/no_namespace.php',
                    8,
                    14
                ),
            ],
            [
                'input' => [$root, 'baz', null],
                'output' => 'no available symbol not found',
            ],
            [
                'input' => [$root, 'strpos', null],
                'output' => 'keyword is an internal function: strpos',
            ],
        ];

        foreach ($cases as $i => $case) {
            $finder = new \PhpCTags\Finder\Position\Function_();
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

    public function testFindWithNamespace()
    {
        $root = realpath(__DIR__.'/../../data');
        $cases = [
            [
                'input' => [$root, 'foo', 'Foo\\Bar'],
                'output' => new \PhpCTags\Position(
                    $root.'/function/with_namespace.php',
                    5,
                    10
                ),
            ],
            [
                'input' => [$root, 'bar', 'Foo\\Bar'],
                'output' => new \PhpCTags\Position(
                    $root.'/function/with_namespace.php',
                    10,
                    10
                ),
            ],
            [
                'input' => [$root, 'baz', 'Foo\\Bar'],
                'output' => new \PhpCTags\Position(
                    $root.'/function/with_namespace.php',
                    15,
                    10
                ),
            ],
        ];

        foreach ($cases as $i => $case) {
            $finder = new \PhpCTags\Finder\Position\Function_();
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
