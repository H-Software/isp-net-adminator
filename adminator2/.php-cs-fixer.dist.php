<?php

$finder = (new PhpCsFixer\Finder())
    ->append([
        './platby-hot-vypis.php'
    ])
    // ->in(__DIR__)
    // ->exclude([
    //     'config',
    //     'database',
    //     'include/font',
    //     'templates_c',
    //     'tests/fixtures',
    //     'plugins',
    //     'temp',
    // ])
    // ->notPath([
    //     'phpstan-baseline.php',
    // ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        'array_indentation' => true,
        // 
        '@PSR12'                                 => true,
        // '@PhpCsFixer'                            => true,
        // 'binary_operator_spaces'                 => ['operators' => ['=' => 'align_single_space', '=>' => 'align_single_space']],
        // 'combine_consecutive_unsets'             => true,
        // 'concat_space'                           => ['spacing' => 'one'],
        // 'no_superfluous_phpdoc_tags'             => false,
        // 'ternary_to_null_coalescing'             => true,
        // 'method_argument_space'                  => ['on_multiline' => 'ensure_fully_multiline'],
        // 'declare_equal_normalize'                => ['space' => 'none'],
        // 'declare_strict_types'                   => true,
        // 'yoda_style'                             => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        // 'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        // 'function_to_constant'                   => true,
        // 'native_constant_invocation'             => true,
        // 'no_alias_functions'                     => true,
        // 'ternary_to_elvis_operator'              => true
    ])
    ->setFinder($finder)
;