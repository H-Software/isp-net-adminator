<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        '/include/font',
        'templates_c',
        'tests/fixtures',
        '/plugins/serializer'
    ])
    ->notPath([
        'test.php',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PhpCsFixer' => true,
    ])
    ->setFinder($finder)
;