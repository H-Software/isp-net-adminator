<?php

$config = [
    'settings' => [
        'displayErrorDetails' => true, // solved by custom handler
        'addContentLengthHeader' => false,

        'logger' => [
            'name' => 'slim-app',
            // 'level' => Monolog\Logger::DEBUG,
            // 'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
];
