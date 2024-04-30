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
];

return $settings;
