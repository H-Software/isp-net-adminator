<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'config',
        'database',
        'include/font',
        'templates_c',
        'tests/fixtures',
        'plugins'
    ])
    ->notPath([
        'test.php',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PhpCsFixer' => true,
        'array_indentation' => true,
        // '@PSR12' => true,
    ])
    ->setFinder($finder)
;