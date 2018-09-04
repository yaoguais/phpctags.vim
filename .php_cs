<?php
$finder = PhpCsFixer\Finder::create()
    ->notPath('tests/data')
    ->notPath('vendor')
    ->in(__DIR__)
    ->name('*.php')
    ->name('PHPJumpToDefinition')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);
$fixers = [
    '@Symfony' => true,
    'binary_operator_spaces' => ['align_double_arrow' => false],
    'array_syntax' => ['syntax' => 'short'],
    'linebreak_after_opening_tag' => true,
    'not_operator_with_successor_space' => true,
    'ordered_imports' => true,
    'phpdoc_order' => true,
];
return PhpCsFixer\Config::create()
    ->setRules($fixers)
    ->setFinder($finder)
    ->setUsingCache(false);