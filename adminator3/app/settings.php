<?php

$settings = [
        'logger' => [
            'name' => 'slim-app',
            // 'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/../../a3-logs/app.log',
        ],
        'twig' => [
            'path' => __DIR__ . '/../resources/views/',
        ],
        'session' => [
            'name' => 'adminator-app',
            'lifetime' => 7200,
            'path' => null,
            'domain' => null,
            'secure' => false,
            'httponly' => true,
            'cache_limiter' => 'nocache',
        ],
];

return $settings;
