<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'config',
        'database',
        'include/font',
        'templates_c',
        'tests/fixtures',
        'plugins',
        'temp',
    ])
    ->notPath([
        'test.php',
        'app/Core/shared/objekt_a2.class.php', // TypeError: Illegal offset type 
        'vlastnici-cross.php', // TypeError: Illegal offset type 
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