<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/boostrap',
        __DIR__ . '/config',
        __DIR__ . '/export',
        __DIR__ . '/include',
        __DIR__ . '/plugins',
        __DIR__ . '/print',
        __DIR__ . '/rss',
    ])
    ->withSkip([
        __DIR__ . '/include/font',
        __DIR__ . '/templates_c',
        __DIR__ . '/tests/fixtures',
        __DIR__ . '/plugins/serializer',
        'test.php',
    ])
    // ->withPreparedSets(psr12: true)
    ->withSets([SetList::SYMPLIFY])
;
