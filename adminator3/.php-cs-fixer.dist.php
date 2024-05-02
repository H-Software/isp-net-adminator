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
        '@PSR12' => true,
        // '@PhpCsFixer' => true,
        'array_indentation' => true,

    ])
    ->setFinder($finder)
;